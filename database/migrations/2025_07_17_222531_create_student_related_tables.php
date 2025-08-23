<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->enum('relationship', ['father', 'mother']);
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('national_id', 14)->unique();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->boolean('is_abroad')->default(false);
            $table->foreignId('country_id')->nullable()->constrained()->restrictOnDelete();
            $table->boolean('living_with_guardian')->default(false);
            $table->foreignId('governorate_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('guardian_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['guardian_id', 'user_id']);
        });

        Schema::create('siblings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('national_id', 14)->unique();
            $table->enum('gender', ['male', 'female']);
            $table->enum('relationship', ['brother', 'sister']);
            $table->enum('academic_level', ['1', '2', '3', '4', '5']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sibling_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sibling_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['sibling_id', 'user_id']);
        });

        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('phone');
            $table->string('relationship');
            $table->foreignId('governorate_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('street');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('sibling_user');
        Schema::dropIfExists('siblings');
        Schema::dropIfExists('guardian_user');
        Schema::dropIfExists('guardians');
    }
};
