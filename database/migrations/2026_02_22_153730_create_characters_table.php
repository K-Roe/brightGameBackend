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
            Schema::create('characters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('hero_class');
                
                // Base Stats
                $table->integer('str')->default(0);
                $table->integer('dex')->default(0);
                $table->integer('agi')->default(0);
                $table->integer('int')->default(0);
                
                // Physical Stats
                $table->decimal('height', 5, 2); // e.g., 180.50 (cm) or 1.80 (m)
                $table->decimal('starting_weight', 8, 2);
                $table->decimal('current_weight', 8, 2);
                $table->decimal('bmi', 5, 2)->nullable(); // Calculated stat
                
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
