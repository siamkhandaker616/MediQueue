<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair migration: adds the booking/token columns that only exist in the
     * edited create-table migration, so databases created before that edit
     * (local dev, Render) get the same schema as fresh installs.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'token_number')) {
                $table->string('token_number')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'queue_position')) {
                $table->unsignedInteger('queue_position')->default(1);
            }
            if (!Schema::hasColumn('appointments', 'estimated_wait_minutes')) {
                $table->unsignedInteger('estimated_wait_minutes')->default(0);
            }
            if (!Schema::hasColumn('appointments', 'fee')) {
                $table->decimal('fee', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('appointments', 'payment_status')) {
                $table->string('payment_status')->default('paid');
            }
            if (!Schema::hasColumn('appointments', 'symptoms')) {
                $table->text('symptoms')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            foreach (['token_number', 'queue_position', 'estimated_wait_minutes', 'fee', 'payment_status', 'symptoms'] as $column) {
                if (Schema::hasColumn('appointments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
