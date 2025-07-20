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
        Schema::create('reservation_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1); // Quantity of equipment assigned
            
            // Equipment status when received by user
            $table->enum('received_status', ['good', 'damaged', 'missing'])->default('good');
            $table->text('received_notes')->nullable(); // Notes about condition when received
            $table->timestamp('received_at')->nullable(); // When user received the equipment
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete(); // Who received it
            
            // Equipment status when returned by user
            $table->enum('returned_status', ['good', 'damaged', 'missing'])->nullable();
            $table->text('returned_notes')->nullable(); // Notes about condition when returned
            $table->timestamp('returned_at')->nullable(); // When user returned the equipment
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete(); // Who returned it
            
            // Overall status
            $table->enum('overall_status', ['pending', 'received', 'returned', 'completed'])->default('pending');
            
            $table->timestamps();
            
            // Ensure unique combination of reservation and equipment
            $table->unique(['reservation_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_equipment');
    }
}; 