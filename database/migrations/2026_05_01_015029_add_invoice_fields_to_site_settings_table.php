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
            $table->string('invoice_logo')->nullable()->after('site_favicon');
            $table->string('invoice_signature')->nullable()->after('invoice_logo');
            $table->string('invoice_company_name')->nullable()->after('invoice_signature');
            $table->text('invoice_company_address')->nullable()->after('invoice_company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['invoice_logo', 'invoice_signature', 'invoice_company_name', 'invoice_company_address']);
        });
    }
};
