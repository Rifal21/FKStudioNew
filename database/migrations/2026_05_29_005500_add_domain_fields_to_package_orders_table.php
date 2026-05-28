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
            $table->boolean('buy_domain')->default(false)->after('business_type');
            $table->string('domain_name')->nullable()->after('buy_domain');
            $table->decimal('domain_price', 15, 2)->nullable()->after('domain_name');
            $table->string('domain_status')->nullable()->after('domain_price'); // pending, registered, failed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $table->dropColumn(['buy_domain', 'domain_name', 'domain_price', 'domain_status']);
        });
    }
};
