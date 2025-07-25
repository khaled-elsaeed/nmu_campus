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
        // First table: Overall equipment checkout record
        Schema::create('equipment_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            
            // Staff who reviewed/handled the equipment return
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Overall status of the equipment checkout
            $table->enum('overall_status', ['pending', 'given', 'returned', 'completed'])->default('pending');
            
            // Overall timestamps
            $table->timestamp('given_at')->nullable(); // When equipment was given to user
            $table->timestamp('returned_at')->nullable(); // When equipment was returned by user
            
            $table->timestamps();
            
            // One checkout record per reservation
            $table->unique('reservation_id');
        });

        // Second table: Individual equipment details
        Schema::create('equipment_checkout_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_checkout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();

            // Quantity of equipment given to user
            $table->integer('quantity_given')->default(1);

            // Equipment status when given to user
            $table->enum('given_status', ['good', 'damaged', 'missing'])->default('good');
            $table->text('given_notes')->nullable(); // Notes about condition when given

            // Quantity of equipment returned by user
            $table->integer('quantity_returned')->nullable();

            // Equipment status when returned by user
            $table->enum('returned_status', ['good', 'damaged', 'missing'])->nullable();
            $table->text('returned_notes')->nullable(); // Notes about condition when returned

            $table->timestamps();
            
            // Ensure unique combination within each checkout
            $table->unique(['equipment_checkout_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_checkout_details');
        Schema::dropIfExists('equipment_checkouts');
    }
};