<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('package_id');
            $table->string('package_name');
            $table->string('package_price');

            // Booking Info
            $table->string('website_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('business_type')->nullable();
            $table->text('client_notes')->nullable();

            $table->string('payment_method')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending');         // pending, paid, completed, cancelled
            $table->string('work_status')->default('pending');   // pending, in_progress, revision, completed, cancelled
            $table->date('delivery_date')->nullable();

            // Payment Gateway
            $table->text('payment_url')->nullable();
            $table->string('payment_reference')->nullable();
            $table->uuid('invoice_id')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_orders');
    }
};
