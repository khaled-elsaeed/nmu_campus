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
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('season', ['fall', 'spring', 'summer', 'winter']);
            $table->string('year', 15);
            $table->enum('semester_number', [1, 2, 3]);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('active')->default(false);
            $table->boolean('current')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
    }
};
