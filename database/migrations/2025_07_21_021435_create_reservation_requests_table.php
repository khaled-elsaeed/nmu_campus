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
        Schema::create('reservation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();

            // Requester information
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('period_type', ['academic', 'calendar']);
            
            $table->foreignId('academic_term_id')->nullable()->constrained()->restrictOnDelete();

            // Requested accommodation details
            $table->enum('requested_accommodation_type', ['room', 'apartment']);
            $table->enum('room_type', ['single', 'double'])->nullable();
            $table->string('requested_double_room_bed_option')->nullable();

            // Requested dates
            $table->date('requested_check_in_date')->nullable();
            $table->date('requested_check_out_date')->nullable();

            // Request management
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');

            // Additional information
            $table->text('special_requirements')->nullable();
            $table->text('resident_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Review information
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->text('rejection_reason')->nullable();

            // Link to created reservation (when approved)
            $table->foreignId('created_reservation_id')->nullable()->constrained('reservations')->nullOnDelete();

            // Criteria points system
            // Each request will be evaluated based on multiple criteria.
            // The total points for the request are stored in this column.
            $table->unsignedInteger('total_points')->default(0);

            // Optionally, you can store the breakdown of points per criterion as JSON
            $table->json('criteria_points_breakdown')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_requests');
    }
};