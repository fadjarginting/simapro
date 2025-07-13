<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NotedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notes = [
            'Belum ada ERF',
            'ERF belum disetujui oleh User di e-DEMS',
            'Proses verifikasi ERF',
            'Approval pengesahan ERF oleh User di e-DEMS',
            'Proses EAT',
            'Approval EAT di e-DEMS',
            'Approval EAT di e-DEMS oleh Ka. Unit',
            'Hold. Pekerjaan ditangguhkan karena ada pekerjaan lainnya yang lebih mendesak sementara sumber daya Fungsi Engineering tidak mencukupi',
            'Hold. Terdapat kebutuhan tambahan atau perubahan informasi yang belum bisa diperoleh dalam masa pelaksanaan pekerjaan, seperti pengukuran yang perlu dilakukan namun terkendala kondisi lapangan atau informasi dari pihak ketiga',
            'Penyusunan dokumen DED',
            'Penyusunan dokumen Technical Assist',
            'Penyusunan dokumen Kajian',
            'Approval dokumen di e-DEMS',
            'Approval dokumen di e-DEMS oleh Ka. Unit',
            'Approval dokumen di e-DEMS oleh Ka. Dept.',
            'Dokumen terkirim ke User melalui e-DEMS',
            'Cancel. ERF tidak sesuai dengan tupoksi Fungsi Engineering',
            'Cancel. ERF tidak sesuai dengan kebijakan manajemen',
            'Cancel. Terdapat ERF yang sama secara judul, latar belakang, maksud, tujuan dan ruang lingkup',
            'Cancel. Informasi yang diminta pada saat verifikasi masih belum dapat dilengkapi oleh Fungsi Peminta setelah 30 hari kalender atau setelah kesepakatan due date pada saat verifikasi',
        ];

        foreach ($notes as $note) {
            Note::create([
            'content' => $note,
            'created_by' => 1,
            ]);
        }
    }
}
