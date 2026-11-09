<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shop (digital + physical products). All money columns are whole rupiah.
 * Prices include PPN; the PPN split is stored per order for the admin only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name_id');
            $table->string('name_en')->nullable();
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('digital')->index(); // digital | physical
            $table->string('slug')->unique();
            $table->string('name_id');
            $table->string('name_en')->nullable();
            $table->text('short_description_id')->nullable();
            $table->text('short_description_en')->nullable();
            $table->longText('description_id')->nullable();
            $table->longText('description_en')->nullable();
            $table->boolean('has_variants')->default(false);
            $table->unsignedBigInteger('min_price')->default(0);
            $table->unsignedBigInteger('max_price')->default(0);
            $table->string('digital_delivery', 20)->nullable(); // file | license_key | link
            $table->text('access_url')->nullable(); // encrypted
            $table->unsignedSmallInteger('max_downloads')->nullable()->default(5); // null = unlimited
            $table->unsignedSmallInteger('download_expiry_days')->nullable();
            $table->text('license_terms_id')->nullable();
            $table->text('license_terms_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt_id')->nullable();
            $table->string('alt_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->string('name_id');
            $table->string('name_en')->nullable();
            $table->unsignedTinyInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('product_option_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_option_id')->constrained()->cascadeOnDelete();
            $table->string('value_id');
            $table->string('value_en')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->nullable()->unique();
            $table->string('title_id')->nullable();
            $table->string('title_en')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('compare_at_price')->nullable();
            $table->unsignedInteger('stock')->nullable(); // null = unlimited
            $table->unsignedInteger('weight_grams')->nullable();
            $table->foreignUuid('product_image_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variant_option_value', function (Blueprint $table) {
            $table->foreignUuid('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_option_value_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_variant_id', 'product_option_value_id'], 'variant_option_value_primary');
        });

        Schema::create('product_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained()->cascadeOnDelete(); // null = every variant
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('version_label')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'product_variant_id']);
        });

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_status', 30)->default('pending')->index();
            $table->string('fulfillment_status', 30)->default('unfulfilled')->index();
            $table->string('payment_channel', 20); // duitku | manual
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->json('shipping_address')->nullable();
            $table->string('courier_code')->nullable();
            $table->string('courier_service')->nullable();
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->string('voucher_code')->nullable();
            $table->unsignedSmallInteger('unique_code')->default(0);
            $table->unsignedBigInteger('total_amount');
            // Admin-only PPN split of the (tax-inclusive) goods amount.
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->unsignedBigInteger('tax_base')->default(0);
            $table->unsignedBigInteger('tax_amount')->default(0);
            $table->foreignUuid('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('needs_attention')->default(false)->index();
            $table->text('attention_reason')->nullable();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_type', 20);
            $table->string('product_name');
            $table->string('variant_title')->nullable();
            $table->string('sku')->nullable();
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedInteger('weight_grams')->nullable();
            $table->timestamps();
        });

        Schema::create('product_license_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('key'); // encrypted
            $table->foreignUuid('shop_order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('shop_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_order_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20); // duitku | manual
            $table->string('merchant_order_id')->nullable()->unique();
            $table->string('method_code')->nullable();
            $table->string('method_name')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('reference')->nullable();
            $table->text('payment_url')->nullable();
            $table->string('status', 20)->default('pending'); // pending | submitted | success | rejected | failed | expired
            $table->string('proof_disk')->nullable();
            $table->string('proof_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_bank')->nullable();
            $table->text('customer_note')->nullable();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('raw_callback')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('digital_entitlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('shop_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('product_license_key_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedSmallInteger('max_downloads')->nullable(); // null = unlimited
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->string('recipient_name');
            $table->string('phone');
            $table->string('province');
            $table->string('city');
            $table->string('district')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->text('address_line');
            $table->string('shipping_area_id')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('scope', 20)->default('all'); // all | package | shop
            $table->unsignedBigInteger('min_purchase')->nullable();
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('shop_enabled')->default(false);
            $table->boolean('shop_physical_enabled')->default(false);
            $table->decimal('shop_tax_rate', 5, 2)->default(11.00);
            $table->unsignedInteger('shop_order_expiry_minutes')->default(1440);
            $table->unsignedInteger('shop_manual_expiry_minutes')->default(1440);
            $table->boolean('shop_manual_payment_enabled')->default(true);
            $table->boolean('shop_manual_unique_code')->default(false);
            $table->string('shop_admin_phone')->nullable(); // WhatsApp for "new order / proof to verify" alerts
            $table->string('shop_origin_area_id')->nullable();
            $table->text('shop_origin_address')->nullable();
            $table->unsignedBigInteger('shop_flat_shipping_cost')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'shop_enabled', 'shop_physical_enabled', 'shop_tax_rate', 'shop_order_expiry_minutes',
                'shop_manual_expiry_minutes', 'shop_manual_payment_enabled', 'shop_manual_unique_code',
                'shop_admin_phone', 'shop_origin_area_id', 'shop_origin_address', 'shop_flat_shipping_cost',
            ]);
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['scope', 'min_purchase']);
        });

        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('digital_entitlements');
        Schema::dropIfExists('shop_payments');
        Schema::dropIfExists('product_license_keys');
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('product_files');
        Schema::dropIfExists('product_variant_option_value');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
