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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('invoice_qris')->nullable()->after('invoice_signature');
            $table->string('invoice_signer_name')->nullable()->after('invoice_company_address');
            $table->json('payment_methods')->nullable()->after('invoice_signer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['invoice_qris', 'invoice_signer_name', 'payment_methods']);
        });
    }
};
