<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();          // e.g. "fa-solid fa-heart-pulse"
            $table->string('room_location')->nullable();  // e.g. "Block A, Floor 2"
            $table->string('floor_number')->nullable();  // legacy support
            $table->string('room_number')->nullable();   // legacy support
            $table->decimal('fee_min', 8, 2)->default(0);
            $table->decimal('fee_max', 8, 2)->default(0);
            $table->string('fee_range')->nullable();     // legacy support (e.g. "500 - 1500")
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};