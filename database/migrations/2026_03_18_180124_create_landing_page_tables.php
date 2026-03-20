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
        // General Site Settings
        Schema::create('site_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('site_name')->default('FKStudio');
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->text('footer_text_id')->nullable();
            $table->text('footer_text_en')->nullable();
            $table->json('social_links')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();
            $table->timestamps();
        });

        // Hero Section
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title_id');
            $table->string('title_en');
            $table->text('subtitle_id');
            $table->text('subtitle_en');
            $table->string('image')->nullable();
            $table->string('cta_text_id')->nullable();
            $table->string('cta_text_en')->nullable();
            $table->string('cta_link')->nullable();
            $table->timestamps();
        });

        // About Section
        Schema::create('about_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title_id');
            $table->string('title_en');
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->json('stats')->nullable(); // JSON: [{"label_id": "Years", "label_en": "Years", "value": "5"}]
            $table->timestamps();
        });

        // Services
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title_id');
            $table->string('title_en');
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon')->nullable(); // CSS class or SVG path
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Projects / Portfolio
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title_id');
            $table->string('title_en');
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->string('category_id')->nullable();
            $table->string('category_en')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('role_id')->nullable();
            $table->string('role_en')->nullable();
            $table->text('content_id')->nullable();
            $table->text('content_en')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('rating')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('services');
        Schema::dropIfExists('about_sections');
        Schema::dropIfExists('hero_sections');
        Schema::dropIfExists('site_settings');
    }
};
