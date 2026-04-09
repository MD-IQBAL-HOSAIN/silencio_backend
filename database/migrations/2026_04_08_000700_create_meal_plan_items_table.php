<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meal_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_id')->constrained('meal_plans')->onDelete('cascade');
            // Slot label shown in UI: breakfast, lunch, dinner, evening, midnight, etc.
            $table->string('meal_name')->nullable();
            $table->time('meal_time')->nullable();
            $table->unsignedBigInteger('meal_id')->nullable();
            $table->timestamps();

            $table->index('meal_plan_id');
            $table->index('meal_name');
            $table->index('meal_id');
            $table->unique(['meal_plan_id', 'meal_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_items');
    }
};
