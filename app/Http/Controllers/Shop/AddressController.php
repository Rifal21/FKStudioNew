<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Shipping address book for physical products. */
class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();

        return view('landing.user.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data) {
            $isFirst = ! $request->user()->addresses()->exists();
            $address = $request->user()->addresses()->create($data + ['is_default' => $isFirst || $request->boolean('is_default')]);
            $this->makeOnlyDefault($address);
        });

        return $this->back($request, app()->getLocale() === 'id' ? 'Alamat disimpan.' : 'Address saved.');
    }

    public function update(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        DB::transaction(function () use ($request, $address) {
            $address->update($this->validated($request) + ['is_default' => $request->boolean('is_default') || $address->is_default]);
            $this->makeOnlyDefault($address);
        });

        return $this->back($request, app()->getLocale() === 'id' ? 'Alamat diperbarui.' : 'Address updated.');
    }

    public function destroy(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        $address->delete();

        if ($address->is_default) {
            $request->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return back()->with('success', app()->getLocale() === 'id' ? 'Alamat dihapus.' : 'Address deleted.');
    }

    private function makeOnlyDefault(UserAddress $address): void
    {
        if ($address->is_default) {
            UserAddress::where('user_id', $address->user_id)->whereKeyNot($address->id)->update(['is_default' => false]);
        }
    }

    private function back(Request $request, string $message)
    {
        return $request->input('return') === 'checkout'
            ? redirect()->route('shop.checkout')->with('success', $message)
            : back()->with('success', $message);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'address_line' => 'required|string|max:500',
        ]);
    }
}
