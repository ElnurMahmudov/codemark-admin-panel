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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique(); // Frontend ID: "express-avto", "gettravel-erp"
            $table->foreignId('category_id')->nullable()->constrained('project_categories')->nullOnDelete();
            $table->string('client'); // Müştəri adı: "ExpressAvto"
            $table->string('year', 4)->default('2026'); // İl: "2026"
            $table->string('bg_light', 9)->default('#FECDD3'); // Light Card Background HEX
            $table->string('bg_dark', 9)->default('#230A10');  // Dark Card Background HEX
            $table->string('image')->nullable(); // Əsas vitrin şəkli (Curator ID)
            $table->json('gallery')->nullable(); // Əlavə skrinşotlar / ekran görüntüləri (Curator IDs)
            $table->string('website_url')->nullable(); // Saytın canlı linki

            // Multilingual (JSON) Fields
            $table->json('title'); // 3 dildə layihə başlığı
            $table->json('description'); // Qısa kart təsviri
            
            // Ətraflı texniki spesifikasiyalar: [{ "label": "Müştəri", "value": "ExpressAvto" }, ...]
            $table->json('specs')->nullable(); 

            // Case Study Bölmələri
            $table->json('challenge')->nullable(); // Biznes problemi / Çətinlik
            $table->json('solution')->nullable();  // Təqdim olunan həll
            $table->json('overview')->nullable();  // Ümumi baxış paraqrafları: string[]
            $table->json('features')->nullable();  // Əsas funksiyalar: string[]
            $table->json('results')->nullable();   // Əldə olunan nəticələr: string[]

            // İdarəetmə və Sıralama
            $table->unsignedInteger('order_column')->default(0)->index(); // Yuxarı/aşağı sürüşdürmə sıralaması
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(true)->index();

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
        Schema::dropIfExists('projects');
    }
};
