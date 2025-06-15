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
        Schema::create('eat', function (Blueprint $table) {
            $table->id();
            $table->string('work_id')->unique();
            $table->integer('total_duration_days')->nullable();
            $table->dateTime('planned_start_date')->nullable();
            $table->dateTime('planned_end_date')->nullable();
            $table->dateTime('actual_start_date')->nullable();
            $table->dateTime('actual_end_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['work_id']); // Prevent duplicate EAT entries for the same work
            
        });

        // tabel pivot untuk menghubungkan eat dengan disiplin
        Schema::create('eat_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained('disciplines')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['eat_id', 'discipline_id']); // Prevent duplicate discipline per EAT
        });

        
        // tabel aktivitas pekerjaan
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained('disciplines')->onDelete('cascade');
            $table->string('activity_name');
            $table->text('activity_description')->nullable();
            $table->integer('duration_days')->nullable();
            $table->dateTime('planned_start_time')->nullable();
            $table->dateTime('planned_end_time')->nullable();
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
            $table->integer('sequence_order')->default(1); // Urutan aktivitas
            $table->timestamps();
        });

        // Tabel Activity PICs - PIC yang ditugaskan ke aktivitas
        Schema::create('activity_pics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role_in_activity', ['primary_pic', 'secondary_pic', 'support'])->default('primary_pic');
            $table->text('responsibilities')->nullable();
            $table->timestamps();
            
            $table->unique(['activity_id', 'user_id']); // Prevent duplicate PIC per activity
        });

        Schema::create('eat_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->foreignId('eat_discipline_id')->constrained('eat_disciplines')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade'); // Lead disiplin yang approve
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->timestamps();
        });

         // Tabel Activity Progress - Tracking progress aktivitas
        Schema::create('activity_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->text('progress_description');
            $table->integer('progress_percentage')->default(0);
            $table->date('progress_date');
            $table->string('attachment_url')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eat');
        Schema::dropIfExists('eat_disciplines');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('activity_pics');
        Schema::dropIfExists('eat_approvals');
        Schema::dropIfExists('activity_progress');

    }
};
