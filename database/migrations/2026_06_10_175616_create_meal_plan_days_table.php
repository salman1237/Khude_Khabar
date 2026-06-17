<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plan_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_id')->constrained()->cascadeOnDelete();
            $table->string('day_name'); // Monday, Tuesday, etc.
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->string('meal_name_bengali');
            $table->json('instructions')->nullable();
            $table->json('nutrition')->nullable(); // {calories, protein, iron}
            $table->decimal('cost_bdt', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plan_days');
    }
};
