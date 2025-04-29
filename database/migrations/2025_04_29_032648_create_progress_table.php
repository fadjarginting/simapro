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

            // Dropdown atau pilihan kategori request (CAPEX/OPEX)
            $table->string('request_category');

            // Status verifikasi ERF
            $table->string('status_verifikasi');

            // PIC (Person In Charge) masing-masing bagian
            $table->string('pic_mekanikal');
            $table->string('pic_sipil');
            $table->string('pic_elinst');
            $table->string('pic_proses');

            // Progress masing-masing bagian (dalam persen)
            $table->unsignedTinyInteger('progress_mekanikal')->default(0);
            $table->unsignedTinyInteger('progress_sipil')->default(0);
            $table->unsignedTinyInteger('progress_elinst')->default(0);
            $table->unsignedTinyInteger('progress_proses')->default(0);

            // Detail progress dan catatan tambahan
            $table->text('detail_progress')->nullable();
            $table->text('note')->nullable();

            $table->timestamps(); // created_at & updated_at
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
