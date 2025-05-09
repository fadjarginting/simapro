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
        Schema::create('progresses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('plant');
            $table->string('work_priority')->nullable();
            $table->string('job_type');
            $table->string('request_category');
            $table->string('no_erf');
            $table->date('erf_approved_date');
            $table->date('erf_clarification_date');
            $table->date('erf_validated_date');
            $table->string('lead_engineering');
            $table->string('pic_mekanikal')->nullable();
            $table->decimal('progress_mekanikal', 5, 2)->nullable();
            $table->string('pic_sipil')->nullable();
            $table->decimal('progress_sipil', 5, 2)->nullable();
            $table->string('pic_elinst')->nullable();
            $table->decimal('progress_elinst', 5, 2)->nullable();
            $table->string('pic_proses')->nullable();
            $table->decimal('progress_proses', 5, 2)->nullable();
            $table->string('uk_peminta');
            $table->string('status_verifikasi');
            $table->date('deadline_initiating');
            $table->date('deadline_executing');
            $table->string('status');
            $table->string('fase');
            $table->string('noted')->nullable();
            $table->text('note')->nullable();
            $table->date('entry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progresses');
    }
};
