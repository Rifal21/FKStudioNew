<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DigitalEntitlement;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/** "Produk Digital Saya": files, license keys and access links the customer has paid for. */
class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $entitlements = DigitalEntitlement::where('user_id', $request->user()->id)
            ->with(['product.images', 'variant', 'licenseKey', 'orderItem.order'])
            ->latest()
            ->get();

        return view('landing.user.downloads', compact('entitlements'));
    }

    /** Counts the download, then hands out a short-lived signed link to the file itself. */
    public function download(Request $request, DigitalEntitlement $entitlement, ProductFile $file)
    {
        $this->authorizeFile($request, $entitlement, $file);
        $id = app()->getLocale() === 'id';

        if (! $entitlement->canDownload()) {
            return back()->with('error', $entitlement->isActive()
                ? ($id ? 'Batas unduhan untuk pembelian ini sudah habis. Hubungi kami bila perlu bantuan.' : 'You have used all downloads for this purchase. Contact us if you need help.')
                : ($id ? 'Akses unduhan untuk pembelian ini sudah tidak aktif.' : 'Download access for this purchase is no longer active.'));
        }

        $entitlement->increment('download_count', 1, ['last_downloaded_at' => now()]);

        return redirect()->to(URL::temporarySignedRoute('user.downloads.file', now()->addMinutes(10), [
            'entitlement' => $entitlement->id,
            'file' => $file->id,
        ]));
    }

    public function file(Request $request, DigitalEntitlement $entitlement, ProductFile $file)
    {
        $this->authorizeFile($request, $entitlement, $file);
        abort_unless($entitlement->isActive(), 403);

        $disk = Storage::disk($file->disk);
        abort_unless($disk->exists($file->path), 404);

        return $disk->download($file->path, $file->original_name);
    }

    private function authorizeFile(Request $request, DigitalEntitlement $entitlement, ProductFile $file): void
    {
        abort_unless($entitlement->user_id === $request->user()->id, 404);
        abort_unless($entitlement->files()->contains('id', $file->id), 404);
    }
}
