<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->string('icon')->nullable()->after('slug');
            $table->string('room_location')->nullable()->after('description');
            $table->decimal('fee_min', 8, 2)->default(0)->after('fee_range');
            $table->decimal('fee_max', 8, 2)->default(0)->after('fee_min');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('specialty')->nullable()->after('specialties');
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['icon', 'room_location', 'fee_min', 'fee_max']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['slug', 'specialty', 'avg_rating', 'rating_count']);
        });
    }
};
