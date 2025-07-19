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
        Schema::create('accommodation_equipment', function (Blueprint $table) {

            $table->primary(['accommodation_id', 'equipment_id']);

            $table->foreignId('accommodation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('equipment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_equipment');
    }
};
