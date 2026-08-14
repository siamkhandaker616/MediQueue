<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained();
            $table->string('name');
            $table->string('email');
            $table->string('photo')->nullable();
            $table->text('qualifications');
            $table->json('specialties')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->json('languages')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
