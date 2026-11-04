<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per reader per article per day. The unique key is what stops a refresh, or the same visitor coming
     * back an hour later, from counting twice. `visitor_hash` is a salted hash, never the raw IP address.
     */
    public function up(): void
    {
        Schema::create('blog_post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->char('visitor_hash', 64);
            $table->date('viewed_on');
            $table->timestamp('created_at')->nullable();

            $table->unique(['blog_post_id', 'visitor_hash', 'viewed_on']);
            $table->index(['blog_post_id', 'viewed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_views');
    }
};
