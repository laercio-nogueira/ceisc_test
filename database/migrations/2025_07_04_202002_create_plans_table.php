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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Básico, Intermediário, Avançado
            $table->string('slug')->unique(); // basic, intermediate, advanced
            $table->text('description');
            $table->decimal('price_monthly', 8, 2);
            $table->decimal('price_semiannual', 8, 2);
            $table->decimal('price_annual', 8, 2);
            $table->integer('screens'); // Número de telas permitidas
            $table->json('features'); // Array de recursos do plano
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
