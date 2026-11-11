<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PromoContent;
use App\Models\Voucher;
use App\Services\Promo\PromoException;
use App\Services\Promo\PromoFormats;
use App\Services\Promo\PromoWriter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function index()
    {
        $promos = PromoContent::with('product:id,name_id,slug', 'author:id,name')->latest()->paginate(20);

        return view('dashboard.shop.promos.index', [
            'promos' => $promos,
            'configured' => filled(config('services.anthropic.key')),
        ]);
    }

    public function create(Request $request)
    {
        return view('dashboard.shop.promos.create', [
            'products' => Product::orderBy('name_id')->get(['id', 'name_id', 'is_active']),
            'selected' => $request->query('product'),
            'vouchers' => $this->vouchers(),
            'formats' => PromoFormats::all(),
            'goals' => PromoWriter::GOALS,
            'tones' => PromoWriter::TONES,
            'configured' => filled(config('services.anthropic.key')),
        ]);
    }

    public function store(Request $request, PromoWriter $writer)
    {
        $data = $request->validate([
            'product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'locale' => 'required|in:id,en',
            'goal' => ['required', Rule::in(array_keys(PromoWriter::GOALS))],
            'tone' => ['required', Rule::in(array_keys(PromoWriter::TONES))],
            'formats' => 'required|array|min:1',
            'formats.*' => ['string', Rule::in(PromoFormats::keys())],
            'voucher_code' => 'nullable|string|max:60',
            'notes' => 'nullable|string|max:1000',
        ], ['formats.required' => 'Pilih minimal satu format konten.']);

        return $this->generate($request, $writer, Product::findOrFail($data['product_id']), $data);
    }

    public function show(PromoContent $promo)
    {
        $promo->load('product', 'author:id,name');

        return view('dashboard.shop.promos.show', [
            'promo' => $promo,
            'formats' => PromoFormats::all(),
            'goals' => PromoWriter::GOALS,
            'tones' => PromoWriter::TONES,
        ]);
    }

    public function update(Request $request, PromoContent $promo, PromoWriter $writer)
    {
        $request->validate([
            'content' => 'required|array',
            'status' => ['required', Rule::in([PromoContent::DRAFT, PromoContent::FINAL])],
        ]);

        $content = PromoFormats::fromForm($request->input('content'), $promo->formats);

        $promo->update([
            'content' => $content,
            'status' => $request->input('status'),
            'warnings' => $writer->review($promo->product, $content, $promo->locale, $this->voucher($promo->voucher_code), $promo->notes),
        ]);

        return back()->with('success', $promo->status === PromoContent::FINAL ? 'Konten disimpan sebagai final.' : 'Draf disimpan.');
    }

    /** A new version from the same settings. Claude sees the current text, so "make it shorter" works. */
    public function regenerate(Request $request, PromoContent $promo, PromoWriter $writer)
    {
        $request->validate(['instruction' => 'nullable|string|max:500']);

        $data = $promo->only(['product_id', 'locale', 'goal', 'tone', 'formats', 'voucher_code', 'notes']);

        return $this->generate($request, $writer, $promo->product, $data, $promo->content, $request->input('instruction'));
    }

    public function destroy(PromoContent $promo)
    {
        $promo->delete();

        return redirect()->route('dashboard.shop.promos.index')->with('success', 'Konten dihapus.');
    }

    private function generate(Request $request, PromoWriter $writer, Product $product, array $data, ?array $previous = null, ?string $instruction = null)
    {
        $limit = (int) config('services.anthropic.promo_daily_limit');
        $today = PromoContent::where('created_by', $request->user()->id)->where('created_at', '>=', now()->startOfDay())->count();

        if ($limit > 0 && $today >= $limit) {
            return back()->withInput()->withErrors(['promo' => "Batas harian {$limit} konten sudah tercapai. Coba lagi besok."]);
        }

        $voucher = $this->voucher($data['voucher_code'] ?? null);
        if (filled($data['voucher_code'] ?? null) && ! $voucher) {
            return back()->withInput()->withErrors(['promo' => 'Voucher tidak ditemukan, sudah tidak berlaku, atau bukan untuk toko.']);
        }

        // The model can take a while; a web request may not give up before it answers.
        set_time_limit(120);

        try {
            $result = $writer->generate($product->load('variants', 'category'), [
                'locale' => $data['locale'],
                'goal' => $data['goal'],
                'tone' => $data['tone'],
                'formats' => $data['formats'],
                'notes' => $data['notes'] ?? null,
                'voucher' => $voucher,
                'previous' => $previous,
                'instruction' => $instruction,
            ]);
        } catch (PromoException $e) {
            return back()->withInput()->withErrors(['promo' => $e->getMessage()]);
        }

        $promo = PromoContent::create([
            'product_id' => $product->id,
            'created_by' => $request->user()->id,
            'locale' => $data['locale'],
            'goal' => $data['goal'],
            'tone' => $data['tone'],
            'formats' => array_values(array_intersect(PromoFormats::keys(), $data['formats'])),
            'voucher_code' => $voucher?->code,
            'notes' => $data['notes'] ?? null,
            'content' => $result['content'],
            'warnings' => $result['warnings'],
            'model' => $result['model'],
            'input_tokens' => $result['input_tokens'],
            'output_tokens' => $result['output_tokens'],
        ]);

        return redirect()->route('dashboard.shop.promos.show', $promo)->with('success', 'Konten selesai dibuat. Periksa dulu sebelum dipakai.');
    }

    private function voucher(?string $code): ?Voucher
    {
        if (blank($code)) {
            return null;
        }

        $voucher = Voucher::where('code', $code)->first();

        return $voucher && $voucher->isValid() && $voucher->appliesTo(Voucher::SCOPE_SHOP) ? $voucher : null;
    }

    private function vouchers()
    {
        return Voucher::where('is_active', true)->orderBy('code')->get()
            ->filter(fn (Voucher $v) => $v->isValid() && $v->appliesTo(Voucher::SCOPE_SHOP))->values();
    }
}
