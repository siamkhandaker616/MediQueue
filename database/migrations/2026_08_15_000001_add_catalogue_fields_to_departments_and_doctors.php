<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The create-table migrations now define these columns directly, so
        // every addition is guarded to keep fresh installs and legacy
        // databases on the same code path without duplicate-column errors.
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'description')) {
                $table->text('description')->nullable();
            } else {
                $table->string('description')->nullable()->change();
            }

            if (!Schema::hasColumn('departments', 'icon')) {
                $table->string('icon')->nullable();
            }
            if (!Schema::hasColumn('departments', 'room_location')) {
                $table->string('room_location')->nullable();
            }
            if (!Schema::hasColumn('departments', 'fee_min')) {
                $table->decimal('fee_min', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('departments', 'fee_max')) {
                $table->decimal('fee_max', 8, 2)->default(0);
            }
        });

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
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['icon', 'room_location', 'fee_min', 'fee_max']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['slug', 'specialty', 'avg_rating', 'rating_count']);
        });
    }
};
