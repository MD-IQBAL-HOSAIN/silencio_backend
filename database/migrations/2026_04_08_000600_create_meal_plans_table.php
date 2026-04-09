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
        // Stores one daily meal-plan header per user.
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Daily target calories for this date.
            $table->unsignedInteger('set_calories')->nullable();
            $table->date('plan_date');
            $table->timestamps();

            // One plan row per user per day.
            $table->unique(['user_id', 'plan_date']);
            $table->index('user_id');
            $table->index('plan_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
