<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
            if (!Schema::hasColumn('doctors', 'specialty')) {
                $table->string('specialty')->nullable();
            }
            if (!Schema::hasColumn('doctors', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->default(0);
            }
            if (!Schema::hasColumn('doctors', 'rating_count')) {
                $table->unsignedInteger('rating_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['slug', 'specialty', 'avg_rating', 'rating_count']);
        });
    }
};