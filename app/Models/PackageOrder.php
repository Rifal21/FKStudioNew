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

    public function provisionTenant()
    {
        if ($this->subdomain && ! $this->tenant_id) {
            $tenant = Tenant::create([
                'id' => $this->subdomain,
                'package_order_id' => $this->id,
                'branding_name' => $this->branding_name,
            ]);

            // For local dev, use localhost. For production, use env config.
            $baseDomain = env('TENANCY_BASE_DOMAIN', 'fkstudio.id');
            $tenant->domains()->create([
                'domain' => $this->subdomain.'.'.$baseDomain,
            ]);

            $this->update(['tenant_id' => $tenant->id]);

            // Seed tenant with the purchaser's user account
            $tenant->run(function () {
                User::create([
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'password' => $this->user->password,
                    'role' => 'super_admin', // They are the super admin of their own tenant
                    'email_verified_at' => $this->user->email_verified_at,
                ]);
            });

            return $tenant;
        }

        return null;
    }
}
