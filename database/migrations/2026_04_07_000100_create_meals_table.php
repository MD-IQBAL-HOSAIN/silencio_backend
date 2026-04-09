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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('time')->nullable();
            $table->string('servings')->nullable();
            $table->string('calories')->nullable();
            $table->string('protein')->nullable();
            $table->string('carbs')->nullable();
            $table->string('fats')->nullable();
            $table->enum('meal_type', ['lunch','dinner','breakfast'])->default('lunch');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Indexes for optimized queries
            $table->index('status', 'meals_status_idx');
            $table->index('meal_type', 'meals_meal_type_idx');
            $table->index(['status', 'meal_type', 'created_at'], 'meals_status_type_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
