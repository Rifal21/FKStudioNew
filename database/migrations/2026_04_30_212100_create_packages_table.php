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
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_id');
            $table->string('name_en');
            $table->string('price')->nullable();
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->json('features_id')->nullable();
            $table->json('features_en')->nullable();
            $table->string('cta_text_id')->nullable();
            $table->string('cta_text_en')->nullable();
            $table->string('cta_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
