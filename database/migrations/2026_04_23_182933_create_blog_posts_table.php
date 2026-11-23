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
            $table->json('title'); // 3 dildə başlıq (az, en, ru)
            $table->json('slug'); // 3 dildə link (az, en, ru)
            $table->json('excerpt')->nullable(); // 3 dildə qısa mətn
            $table->json('author')->nullable(); // 3 dildə müəllif (Default: CODEMARK)
            $table->json('content')->nullable(); // 3 dildə əsas məzmun (Texteditor / RichEditor)
            
            // Qeyri-dil sahələri
            $table->string('image')->nullable(); // Əsas poster şəkli (Curator ID)
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
