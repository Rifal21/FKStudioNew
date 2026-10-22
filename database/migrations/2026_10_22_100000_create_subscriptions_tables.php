<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->text('client_address')->nullable();

            // Jadwal: tanggal tagih tiap bulan (29-31 dipotong ke hari terakhir bulan itu).
            $table->unsignedTinyInteger('billing_day');
            $table->unsignedSmallInteger('due_days')->default(7);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_date')->nullable()->index();
            $table->string('status')->default('active'); // active, paused, ended

            $table->decimal('discount', 15, 2)->default(0);
            $table->string('discount_type')->default('percent');
            $table->decimal('tax', 15, 2)->default(0);
            $table->string('tax_type')->default('percent');
            $table->text('notes')->nullable();
            $table->boolean('auto_send')->default(false);
            $table->timestamps();
        });

        Schema::create('subscription_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('client_phone')->nullable()->after('client_email');
            $table->foreignUuid('subscription_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->date('billing_period')->nullable();
            $table->timestamp('wa_sent_at')->nullable();
            $table->text('wa_error')->nullable();
            $table->unique(['subscription_id', 'billing_period']);
        });

        // Item order must survive a reload: ids are UUIDs, so they cannot be used to sort.
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->unsignedInteger('position')->default(0);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('waha_enabled')->default(false);
            $table->string('waha_url')->nullable();
            $table->text('waha_api_key')->nullable();
            $table->string('waha_session')->nullable();
            $table->text('waha_invoice_template')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['waha_enabled', 'waha_url', 'waha_api_key', 'waha_session', 'waha_invoice_template']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['subscription_id', 'billing_period']);
            $table->dropConstrainedForeignId('subscription_id');
            $table->dropColumn(['client_phone', 'billing_period', 'wa_sent_at', 'wa_error']);
        });

        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
    }
};
