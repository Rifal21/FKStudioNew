<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Real business details for Google and AI. Left empty = nothing is published.
            $table->string('biz_street')->nullable();
            $table->string('biz_city')->nullable();
            $table->string('biz_region')->nullable();
            $table->string('biz_postal_code', 10)->nullable();
            $table->string('biz_opening_hours')->nullable();   // schema.org format, e.g. "Mo-Fr 09:00-17:00"
            $table->string('biz_price_range')->nullable();     // e.g. "Rp 7.500.000 - Rp 18.500.000"
            $table->string('bing_verification')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['biz_street', 'biz_city', 'biz_region', 'biz_postal_code', 'biz_opening_hours', 'biz_price_range', 'bing_verification']);
        });
    }
};
