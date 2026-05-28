<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('package_orders', 'website_name')) {
                $table->string('website_name')->nullable()->after('package_price');
            }
            if (!Schema::hasColumn('package_orders', 'website_url')) {
                $table->string('website_url')->nullable()->after('website_name');
            }
            if (!Schema::hasColumn('package_orders', 'business_type')) {
                $table->string('business_type')->nullable()->after('website_url');
            }
            if (!Schema::hasColumn('package_orders', 'client_notes')) {
                $table->text('client_notes')->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('package_orders', 'work_status')) {
                $table->string('work_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('package_orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('work_status');
            }
            if (!Schema::hasColumn('package_orders', 'payment_url')) {
                $table->text('payment_url')->nullable()->after('delivery_date');
            }
            if (!Schema::hasColumn('package_orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_url');
            }
            if (!Schema::hasColumn('package_orders', 'invoice_id')) {
                $table->uuid('invoice_id')->nullable()->after('payment_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $columns = [];
            
            if (Schema::hasColumn('package_orders', 'website_name')) {
                $columns[] = 'website_name';
            }
            if (Schema::hasColumn('package_orders', 'website_url')) {
                $columns[] = 'website_url';
            }
            if (Schema::hasColumn('package_orders', 'business_type')) {
                $columns[] = 'business_type';
            }
            if (Schema::hasColumn('package_orders', 'client_notes')) {
                $columns[] = 'client_notes';
            }
            if (Schema::hasColumn('package_orders', 'work_status')) {
                $columns[] = 'work_status';
            }
            if (Schema::hasColumn('package_orders', 'delivery_date')) {
                $columns[] = 'delivery_date';
            }
            if (Schema::hasColumn('package_orders', 'payment_url')) {
                $columns[] = 'payment_url';
            }
            if (Schema::hasColumn('package_orders', 'payment_reference')) {
                $columns[] = 'payment_reference';
            }
            if (Schema::hasColumn('package_orders', 'invoice_id')) {
                $columns[] = 'invoice_id';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
