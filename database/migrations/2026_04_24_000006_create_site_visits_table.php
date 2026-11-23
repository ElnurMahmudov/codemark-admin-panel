<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->nullableMorphs('visitable');
            $table->string('device_type', 20)->default('desktop')->index(); // desktop, mobile, tablet
            $table->string('browser', 50)->nullable()->index(); // Chrome, Safari, etc.
            $table->string('os', 50)->nullable(); // iOS, Android, Windows, macOS, Linux
            $table->string('referer')->nullable(); // google.com, direct, instagram.com
            $table->string('country', 10)->nullable();
            $table->timestamps();

            $table->index(['created_at', 'url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
