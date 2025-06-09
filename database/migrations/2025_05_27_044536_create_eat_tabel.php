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
            $table->text('description')->nullable();
            $table->integer('total_duration_days')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('status', ['draft', 'waiting_approval', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
        });

        // disiplin terkait dengan EAT
        Schema::create('disciplines', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        
        // tabel aktivitas pekerjaan
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->string('activity_name');
            $table->integer('duration_days')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->foreignId('discipline_id')->nullable()->constrained('disciplines')->onDelete('set null');
            $table->text('activity_description')->nullable();
            $table->timestamps();
        });

        // tabel pivot untuk assigned users ke aktivitas
        Schema::create('activity_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('discipline_id')->nullable()->constrained('disciplines')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // tabel persetujuan eat
        Schema::create('eat_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eat_id')->constrained('eat')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->dateTime('approval_date')->nullable();
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
        Schema::dropIfExists('activity_user');
        Schema::dropIfExists('eat_approvals');
    }
};
