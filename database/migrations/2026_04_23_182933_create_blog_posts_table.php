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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            
            // Multilingual (JSON) Fields
            $table->json('slug'); // {"az": "muasir-veb-standartlari", "en": "modern-web-standards", "ru": "standarty-veb-sayta"}
            $table->json('title'); // 3 dildə başlıq
            $table->json('excerpt'); // Qısa təsvir
            $table->json('author')->nullable(); // Default: CODEMARK
            
            // Dinamik Məzmun Blokları (Sections: heading, paragraphs, bullets)
            $table->json('sections')->nullable(); // Filament Repeater / Builder strukturu
            
            // Qeyri-dil sahələri
            $table->string('image')->nullable(); // Əsas poster şəkli (WebP / Curator ID)
            $table->unsignedInteger('order_column')->default(0)->index(); // Yuxarı/aşağı sürüşdürmə sıralaması
            $table->date('published_at')->nullable()->index();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('views_count')->default(0);

            // SEO Meta sahələri
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
