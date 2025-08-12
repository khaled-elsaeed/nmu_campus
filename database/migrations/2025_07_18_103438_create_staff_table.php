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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('unit_type', ['faculty', 'administrative', 'campus_unit']);
            $table->foreignId('faculty_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('campus_unit_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('national_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
