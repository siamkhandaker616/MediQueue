<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['icon', 'room_location', 'fee_min', 'fee_max']);
        });
    }
};