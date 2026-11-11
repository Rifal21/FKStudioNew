<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('locale', 5)->default('id');
            $table->string('goal', 30);
            $table->string('tone', 30);
            $table->json('formats');
            $table->string('voucher_code')->nullable();
            $table->text('notes')->nullable();
            $table->json('content');
            $table->json('warnings')->nullable();
            $table->string('status', 20)->default('draft'); // draft | final
            $table->string('model', 60)->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['created_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_contents');
    }
};
