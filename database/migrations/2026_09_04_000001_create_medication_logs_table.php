<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->enum('slot', ['morning', 'afternoon', 'evening']);
            $table->enum('status', ['taken', 'skipped', 'missed']);
            $table->timestamp('logged_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'prescription_item_id', 'scheduled_date', 'slot']);
            $table->index(['user_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_logs');
    }
};
