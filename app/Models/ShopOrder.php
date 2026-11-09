<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class ShopOrder extends Model
{
    use HasUuid;

    public const PENDING = 'pending';
    public const AWAITING_VERIFICATION = 'awaiting_verification';
    public const PAID = 'paid';
    public const EXPIRED = 'expired';
    public const CANCELLED = 'cancelled';
    public const REFUNDED = 'refunded';

    public const UNFULFILLED = 'unfulfilled';
    public const PROCESSING = 'processing';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
    public const FULFILLED = 'fulfilled';

    public const CHANNEL_DUITKU = 'duitku';
    public const CHANNEL_MANUAL = 'manual';

    protected $guarded = [];

    protected $casts = [
        'shipping_address' => 'array',
        'shipping_cost' => 'integer',
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'unique_code' => 'integer',
        'total_amount' => 'integer',
        'tax_rate' => 'decimal:2',
        'tax_base' => 'integer',
        'tax_amount' => 'integer',
        'needs_attention' => 'boolean',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /** PPN figures are for the admin only and never leave the server in customer-facing output. */
    protected $hidden = ['tax_rate', 'tax_base', 'tax_amount', 'admin_notes'];

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ShopOrderItem::class)->orderBy('created_at');
    }

    public function payments()
    {
        return $this->hasMany(ShopPayment::class)->latest();
    }

    public function latestPayment()
    {
        return $this->hasOne(ShopPayment::class)->latestOfMany();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function entitlements()
    {
        return $this->hasManyThrough(DigitalEntitlement::class, ShopOrderItem::class);
    }

    /** SO-YYYYMMDD-NNNN, counting per day. */
    public static function nextNumber(): string
    {
        $prefix = 'SO-'.now()->format('Ymd').'-';

        $last = static::where('order_number', 'like', $prefix.'%')
            ->pluck('order_number')
            ->map(fn ($n) => (int) substr($n, strlen($prefix)))
            ->max() ?? 0;

        return $prefix.str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }

    /** Creates the order with a fresh number, retrying if two checkouts race for the same one. */
    public static function createNumbered(array $attributes): self
    {
        for ($attempt = 1; ; $attempt++) {
            try {
                return static::create(['order_number' => static::nextNumber()] + $attributes);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt >= 5) {
                    throw $e;
                }
            }
        }
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAID;
    }

    public function isOpen(): bool
    {
        return in_array($this->payment_status, [self::PENDING, self::AWAITING_VERIFICATION], true);
    }

    public function canPay(): bool
    {
        return $this->payment_status === self::PENDING && (! $this->expires_at || $this->expires_at->isFuture());
    }

    public function hasPhysicalItems(): bool
    {
        return $this->items->contains('product_type', Product::PHYSICAL);
    }

    public function hasDigitalItems(): bool
    {
        return $this->items->contains('product_type', Product::DIGITAL);
    }

    public function goodsAmount(): int
    {
        return max(0, $this->subtotal - $this->discount_amount);
    }

    public static function paymentStatusLabels(string $locale = 'id'): array
    {
        return [
            'id' => [
                self::PENDING => 'Menunggu pembayaran',
                self::AWAITING_VERIFICATION => 'Menunggu verifikasi',
                self::PAID => 'Lunas',
                self::EXPIRED => 'Kedaluwarsa',
                self::CANCELLED => 'Dibatalkan',
                self::REFUNDED => 'Dikembalikan',
            ],
            'en' => [
                self::PENDING => 'Awaiting payment',
                self::AWAITING_VERIFICATION => 'Awaiting verification',
                self::PAID => 'Paid',
                self::EXPIRED => 'Expired',
                self::CANCELLED => 'Cancelled',
                self::REFUNDED => 'Refunded',
            ],
        ][$locale] ?? [];
    }

    public static function fulfillmentStatusLabels(string $locale = 'id'): array
    {
        return [
            'id' => [
                self::UNFULFILLED => 'Belum diproses',
                self::PROCESSING => 'Dikemas',
                self::SHIPPED => 'Dikirim',
                self::DELIVERED => 'Diterima',
                self::FULFILLED => 'Selesai',
            ],
            'en' => [
                self::UNFULFILLED => 'Not processed',
                self::PROCESSING => 'Packing',
                self::SHIPPED => 'Shipped',
                self::DELIVERED => 'Delivered',
                self::FULFILLED => 'Completed',
            ],
        ][$locale] ?? [];
    }

    public function paymentStatusLabel(?string $locale = null): string
    {
        return static::paymentStatusLabels($locale ?? app()->getLocale())[$this->payment_status] ?? $this->payment_status;
    }

    public function fulfillmentStatusLabel(?string $locale = null): string
    {
        return static::fulfillmentStatusLabels($locale ?? app()->getLocale())[$this->fulfillment_status] ?? $this->fulfillment_status;
    }

    public function paymentStatusVariant(): string
    {
        return match ($this->payment_status) {
            self::PAID => 'success',
            self::AWAITING_VERIFICATION => 'info',
            self::PENDING => 'warning',
            self::REFUNDED => 'neutral',
            default => 'danger',
        };
    }
}
