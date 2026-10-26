<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sistem_logs');
    }

    public function down(): void
    {
        Schema::create('sistem_logs', function (Blueprint $table) {
            $table->id();
            $table->string('aktivitas');
            $table->string('kategori')->default('umum');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
};
