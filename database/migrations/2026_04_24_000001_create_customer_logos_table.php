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
        Schema::create('customer_logos', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Şirkət adı: "Troya", "VIP Mail"
            $table->string('light_logo'); // Ağ/açıq loqo fayl yolu (dark theme üçün)
            $table->string('dark_logo');  // Tünd loqo fayl yolu (light theme üçün)
            $table->string('website_url')->nullable(); // Müştərinin veb saytı (istəyə bağlı)
            $table->unsignedInteger('order_column')->default(0)->index(); // Drag & drop sıralama
            $table->boolean('is_active')->default(true)->index(); // Aktiv / Deaktiv
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_logos');
    }
};
