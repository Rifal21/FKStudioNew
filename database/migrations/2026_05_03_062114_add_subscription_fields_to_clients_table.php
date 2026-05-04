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
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('is_server_subscribed')->default(false)->after('url');
            $table->integer('billing_date')->nullable()->after('is_server_subscribed');
            $table->decimal('subscription_price', 15, 2)->default(0)->after('billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['is_server_subscribed', 'billing_date', 'subscription_price']);
        });
    }
};
