<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    use HasFactory, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'package_order_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function dpInvoice()
    {
        return $this->belongsTo(Invoice::class, 'dp_invoice_id');
    }

    public function finalInvoice()
    {
        return $this->belongsTo(Invoice::class, 'final_invoice_id');
    }

    /**
     * Work status label in Indonesian/English.
     */
    public function workStatusLabel(string $locale = 'id'): string
    {
        $labels = [
            'id' => [
                'pending'     => 'Menunggu',
                'in_progress' => 'Sedang Dikerjakan',
                'revision'    => 'Revisi',
                'completed'   => 'Selesai',
                'cancelled'   => 'Dibatalkan',
            ],
            'en' => [
                'pending'     => 'Pending',
                'in_progress' => 'In Progress',
                'revision'    => 'Revision',
                'completed'   => 'Completed',
                'cancelled'   => 'Cancelled',
            ],
        ];

        return $labels[$locale][$this->work_status] ?? ucfirst($this->work_status);
    }

    /**
     * Work status step index (0-based) for stepper UI.
     */
    public function workStatusStep(): int
    {
        return match ($this->work_status) {
            'pending'     => 0,
            'in_progress' => 1,
            'revision'    => 2,
            'completed'   => 3,
            default       => 0,
        };
    }

    /**
     * Provision tenant (Legacy method from tenancy architecture, kept empty to avoid crashes).
     */
    public function provisionTenant()
    {
        return true;
    }
}
