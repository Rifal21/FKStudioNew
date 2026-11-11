<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'size' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Types the browser may show inside the admin origin. Everything else is only ever downloaded:
     * an uploaded .html or .svg opened inline would run its scripts with the admin's session.
     */
    public const INLINE_MIMES = [
        'application/pdf',
        'image/png', 'image/jpeg', 'image/gif', 'image/webp',
        'text/plain',
        'audio/mpeg', 'audio/ogg', 'audio/wav',
        'video/mp4', 'video/webm',
    ];

    private const INLINE_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt', 'mp3', 'ogg', 'wav', 'mp4', 'webm'];

    /** Cheap, name-based guess used for the "open" button; the controller re-checks the real content type. */
    public function looksViewable(): bool
    {
        return in_array(strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION)), self::INLINE_EXTENSIONS, true);
    }

    public function humanSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->size;
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return number_format($size, $i === 0 ? 0 : 1, ',', '.').' '.$units[$i];
    }
}
