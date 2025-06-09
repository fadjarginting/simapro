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
        // Create notes table first since works references it
        Schema::create('noted', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Tabel utama untuk pekerjaan
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('erf_number', 50)->unique()->nullable();
            $table->text('description');
            $table->date('entry_date');

            // Informasi Dasar
            $table->foreignId('plant_id')->nullable()->constrained('plants')->onDelete('set null');
            $table->string('requester_unit', 100)->nullable();
            $table->enum('work_priority', ['HIGH', 'MEDIUM'])->nullable();
            $table->enum('work_type', ['FEED/DED', 'Kajian Engineering', 'Technical Assist'])->nullable();
            $table->enum('request_category', ['CAPEX', 'OPEX'])->nullable();

            // Timeline
            $table->date('erf_approved_date')->nullable();
            $table->date('clarification_date')->nullable();
            $table->date('erf_validated_date')->nullable();
            $table->date('initiating_target_date')->nullable();

            // EAT (Executing Actual/Target)
            $table->date('executing_start_date')->nullable();
            $table->date('executing_target_date')->nullable();
            $table->date('executing_actual_date')->nullable();

            // Status
            $table->enum('verification_status', ['Belum Verifikasi', 'Finish Verifikasi', 'In Progress Verifikasi'])->nullable();
            $table->enum('project_status', ['Not Started', 'In Progress', 'Finish', 'On Hold', 'Cancelled'])->nullable();
            $table->enum('current_phase', ['Not started', 'Initiating', 'Executing', 'Closing', 'Hold', 'Reject'])->nullable();
            
            // Keterangan Progress
            $table->foreignId('note_id')->nullable()->constrained('noted')->onDelete('set null');

            // Lead Engineer
            $table->foreignId('lead_engineer_id')->nullable()->constrained('users')->onDelete('cascade');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('slug')->unique(); // Slug untuk pekerjaan, harus unik
            $table->timestamps();
        });

        // tabel dokumen erf
        Schema::create('erf_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained('works')->onDelete('cascade');
            
            $table->string('file_name');
            $table->string('file_url');
            $table->timestamp('uploaded_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // work progress table
        Schema::create('work_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained('works')->onDelete('cascade');
            $table->text('progress_description');
            $table->integer('progress_percentage')->default(0);
            $table->date('progress_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('attachment_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
        Schema::dropIfExists('erf_documents');
        Schema::dropIfExists('pic_pekerjaan');
        Schema::dropIfExists('work_progress');
    }
};
