<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('time_slot');                          // Changed to string for "09:00 - 09:30"
            $table->string('status')->default('scheduled');
            $table->string('token_number')->nullable();           // FR-04: Digital Token
            $table->unsignedInteger('queue_position')->default(1);// FR-04: Queue position
            $table->unsignedInteger('estimated_wait_minutes')->default(0);
            $table->decimal('fee', 10, 2)->default(0);            // FR-09: Fee
            $table->string('payment_status')->default('paid');
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->text('symptoms')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'date', 'time_slot']);
            $table->index(['patient_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};