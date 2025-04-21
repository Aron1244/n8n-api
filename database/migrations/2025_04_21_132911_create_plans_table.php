<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePlansTable extends Migration
{
    public function up()
    {
        // Crear la tabla de planes
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Nombre del plan (e.g., Básico, Premium)
            $table->text('description')->nullable();  // Descripción del plan
            $table->decimal('price', 10, 2);   // Precio del plan
            $table->json('features');          // Características en formato JSON
            $table->timestamps();              // Fechas de creación y actualización
        });

        // Insertar los planes predeterminados
        DB::table('plans')->insert([
            [
                'name' => 'Basic Plan',
                'description' => 'Perfect for getting started with AI',
                'price' => 29.00,
                'features' => json_encode([
                    '100,000 tokens per month',
                    'Standard queries with the AI agent',
                    'Access to basic features',
                    'Email support',
                    'Standard response time',
                    'Basic query history'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'For users who need more power',
                'price' => 99.00,
                'features' => json_encode([
                    '500,000 tokens per month',
                    'Advanced queries with the AI agent',
                    'Access to all features',
                    '24/7 priority support',
                    'Priority processing',
                    'Unlimited query history',
                    'Early access to new features',
                    'Advanced AI models'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        // Eliminar la tabla de planes
        Schema::dropIfExists('plans');
    }
}
