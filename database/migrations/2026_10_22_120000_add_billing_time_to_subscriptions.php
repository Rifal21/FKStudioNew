<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // HH:MM in WIB. Existing subscriptions keep the old fixed 06:00 run.
            $table->string('billing_time', 5)->default('06:00')->after('billing_day');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedSmallInteger('wa_attempts')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $table) => $table->dropColumn('wa_attempts'));
        Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn('billing_time'));
    }
};
