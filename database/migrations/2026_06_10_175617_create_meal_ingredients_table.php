<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_day_id')->constrained()->cascadeOnDelete();
            $table->string('ingredient_name');
            $table->string('quantity');
            $table->string('unit')->nullable();
            $table->string('category')->nullable(); // vegetables, protein, dairy, spices
            $table->decimal('estimated_price_bdt', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_ingredients');
    }
};
