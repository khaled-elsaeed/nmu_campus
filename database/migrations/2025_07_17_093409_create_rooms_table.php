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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->enum('type', ['single', 'double']);
            $table->integer('capacity');
            $table->integer('current_occupancy')->default(0);
            $table->integer('available_capacity');
            $table->enum('purpose', ['housing', 'staff_housing', 'office', 'storage'])->default('housing');
            $table->enum('occupancy_status', ['available','partially_occupied',  'occupied', 'maintenance', 'reserved']);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->unique(['apartment_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
