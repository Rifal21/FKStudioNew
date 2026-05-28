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
            $table->string('payment_scheme')->default('full')->after('payment_method'); // full, dp
            $table->decimal('dp_amount', 15, 2)->default(0)->after('payment_scheme');
            $table->decimal('remaining_balance', 15, 2)->default(0)->after('dp_amount');
            $table->uuid('dp_invoice_id')->nullable()->after('remaining_balance');
            $table->uuid('final_invoice_id')->nullable()->after('dp_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_scheme', 'dp_amount', 'remaining_balance', 'dp_invoice_id', 'final_invoice_id']);
        });
    }
};
