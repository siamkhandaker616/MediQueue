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
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('slug')->unique()->nullable();        // Added for URL slugs
            $table->string('photo')->nullable();
            $table->text('qualifications');
            $table->string('specialty')->nullable();             // Added for primary specialty
            $table->json('specialties')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);     // Added for star ratings (e.g. 4.8)
            $table->unsignedInteger('rating_count')->default(0); // Added for total reviews
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