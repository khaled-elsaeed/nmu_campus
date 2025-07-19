<?php

// ONE-TO-ONE RELATIONSHIP APPROACH
// Migration for students table
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('student_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('whatsapp', 20)->nullable();            
            $table->string('parent_name')->nullable();
            $table->string('parent_mobile', 20)->nullable();
            $table->string('parent_email')->nullable();
            $table->string('parent_country_name', 100)->nullable();

            // Certificate information
            $table->string('certificate_type_name')->nullable();
            $table->string('cert_country_name', 100)->nullable();
            $table->string('cert_year_name', 50)->nullable();
            
            // Brother/sibling information
            $table->string('brother')->nullable();
            $table->string('brother_name')->nullable();
            $table->string('brother_faculty', 10)->nullable();
            $table->string('brother_faculty_name')->nullable();
            $table->string('brother_level')->nullable();
            
            // Application information
            $table->string('candidated_faculty_name')->nullable();
            $table->decimal('actual_score', 6, 2)->nullable();
            $table->decimal('actual_percent', 5, 2)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_archives');
    }
};
