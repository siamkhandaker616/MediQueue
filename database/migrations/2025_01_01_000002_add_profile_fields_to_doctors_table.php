<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('user_id')
                  ->constrained()->nullOnDelete();

            $table->string('slug')->unique()->nullable()->after('department_id');
            $table->string('photo')->nullable();               // storage path
            $table->string('specialty')->nullable();           // e.g. "Cardiologist"
            $table->text('qualifications')->nullable();        // e.g. "MBBS, FCPS (Cardiology)"
            $table->unsignedSmallInteger('experience_years')->default(0);
            $table->json('languages')->nullable();              // ["English","Bangla"]
            $table->decimal('consultation_fee', 8, 2)->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);    // denormalized, updated when FR-19 reviews land
            $table->unsignedInteger('rating_count')->default(0);
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn([
                'slug', 'photo', 'specialty', 'qualifications', 'experience_years',
                'languages', 'consultation_fee', 'avg_rating', 'rating_count', 'bio', 'is_active',
            ]);
        });
    }
};
