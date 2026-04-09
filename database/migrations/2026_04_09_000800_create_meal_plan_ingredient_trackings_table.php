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
        // Stores date-wise ingredient market checklist for each authenticated user.
        Schema::create('meal_plan_ingredient_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_plan_id')->nullable()->constrained('meal_plans')->nullOnDelete();
            $table->date('plan_date');
            $table->unsignedBigInteger('ingredient_id');
            $table->boolean('is_purchased')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('plan_date');
            $table->index('ingredient_id');
            $table->index('is_purchased');
            $table->unique(['user_id', 'plan_date', 'ingredient_id'], 'mpit_user_date_ing_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_ingredient_trackings');
    }
};
