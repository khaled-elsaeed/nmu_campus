<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_archives', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('external_id')->nullable()->unique();

            // Basic student info
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('nationality_name', 100)->nullable();

            // Address info
            $table->string('govern', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('street')->nullable();

            // Parent info
            $table->string('parent_name')->nullable();
            $table->string('parent_mobile', 20)->nullable();
            $table->string('parent_email')->nullable();
            $table->string('parent_country_name', 100)->nullable();

            // Certificate info
            $table->string('certificate_type_name')->nullable();
            $table->string('cert_country_name', 100)->nullable();
            $table->string('cert_year_name', 50)->nullable();

            // Sibling info
            $table->string('brother')->nullable(); // نعم / لا
            $table->string('brother_name')->nullable();
            $table->string('brother_faculty', 10)->nullable();
            $table->string('brother_faculty_name')->nullable();
            $table->string('brother_level')->nullable();

            // Application info
            $table->string('candidated_faculty_name')->nullable();
            $table->decimal('actual_score', 6, 2)->nullable();
            $table->decimal('actual_percent', 5, 2)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_archives');
    }
};
