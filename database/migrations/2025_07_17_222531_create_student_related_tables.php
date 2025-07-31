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
        // Create parents table
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('relation', ['father', 'mother']);
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('national_id', 14)->unique();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->boolean('is_abroad')->default(false);
            $table->foreignId('governorate_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });

        // Create siblings table
        Schema::create('siblings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('national_id', 14)->unique();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->enum('relationship', ['brother', 'sister']);
            $table->enum('academic_level', ['1', '2', '3', '4', '5']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Create emergency contacts table
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('phone');
            $table->enum('relationship', [
                'uncle', 'aunt', 'grandparent', 'cousin', 'nephew', 'niece'
            ]);
            $table->foreignId('governorate_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('address');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('siblings');
        Schema::dropIfExists('parents');
    }
};