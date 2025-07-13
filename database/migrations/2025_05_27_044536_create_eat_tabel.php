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
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
                $table->timestamps();
            });
        
        // tabel aktivitas pekerjaan
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained('disciplines')->onDelete('cascade');
            $table->string('activity_name');
            $table->text('activity_description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Tabel Activity PICs - PIC yang ditugaskan ke aktivitas
        Schema::create('activity_pics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['activity_id', 'user_id']); // Pastikan satu PIC hanya bisa ditugaskan ke satu aktivitas
        });

         // Tabel Activity Progress - Tracking progress aktivitas
        Schema::create('activity_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->text('progress_description');
            $table->integer('progress_percentage')->default(0);
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
        Schema::create('eat_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade'); // Lead disiplin yang approve
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eat');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('activity_pics');
        Schema::dropIfExists('eat_approvals');
        Schema::dropIfExists('activity_progress');

    }
};
