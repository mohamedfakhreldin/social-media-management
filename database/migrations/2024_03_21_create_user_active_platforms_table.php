<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_active_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
  
            $table->timestamps();

            // Ensure a user can only have one active connection per platform
            $table->unique(['user_id', 'platform_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_active_platforms');
    }
}; 