<?php

namespace Database\Seeders;

use App\Models\Noted;
use App\Models\Plant;
use App\Models\Classification;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plants = [
            ['id' => 1, 'name' => 'INDARUNG UNIT I', 'slug' => 'indarung-i'],
            ['id' => 2, 'name' => 'INDARUNG UNIT II', 'slug' => 'indarung-ii'],
            ['id' => 3, 'name' => 'INDARUNG UNIT III', 'slug' => 'indarung-iii'],
            ['id' => 4, 'name' => 'INDARUNG UNIT IV (EX. III B)', 'slug' => 'indarung-ivb'],
            ['id' => 5, 'name' => 'INDARUNG UNIT IV (EX. III C)', 'slug' => 'indarung-ivc'],
            ['id' => 6, 'name' => 'INDARUNG UNIT V', 'slug' => 'indarung-v'],
            ['id' => 7, 'name' => 'INDARUNG UNIT VI', 'slug' => 'indarung-vi'],
            ['id' => 8, 'name' => 'DESTINATION PACKING PLANT', 'slug' => 'destination-packing-plant'],
            ['id' => 9, 'name' => 'PACKING PLANT CIWANDAN', 'slug' => 'packing-plant-ciwandan'],
            ['id' => 10, 'name' => 'PACKING PLANT DUMAI', 'slug' => 'packing-plant-dumai'],
            ['id' => 11, 'name' => 'PACKING PLANT LAMPUNG', 'slug' => 'packing-plant-lampung'],
            ['id' => 12, 'name' => 'BATA INTERLOCK PRESISI PLANT', 'slug' => 'bata-interlock-presisi-plant'],
            ['id' => 13, 'name' => 'GENERAL', 'slug' => 'general'],
        ];

        foreach ($plants as $plant) {
            Plant::create([
                'name' => $plant['name'],
                'slug' => $plant['slug'],
            ]);
        }

        // noted table
        $noted = [
            ['noted_code' => '01', 'name' => 'Belum ada ERF', 'slug' => 'belum-ada-erf'],
            ['noted_code' => '02', 'name' => 'ERF belum disetujui oleh User di e-DEMS', 'slug' => 'erf-belum-disetujui-oleh-user-di-e-dems'],
            ['noted_code' => '03', 'name' => 'Proses verifikasi ERF', 'slug' => 'proses-verifikasi-erf'],
            ['noted_code' => '04', 'name' => 'Approval pengesahan ERF oleh User di e-DEMS', 'slug' => 'approval-pengesahan-erf-oleh-user-di-e-dems'],
            ['noted_code' => '05', 'name' => 'Proses EAT', 'slug' => 'proses-eat'],
            ['noted_code' => '06', 'name' => 'Approval EAT di e-DEMS', 'slug' => 'approval-eat-di-e-dems'],
            ['noted_code' => '07', 'name' => 'Approval EAT di e-DEMS oleh Ka. Unit', 'slug' => 'approval-eat-di-e-dems-oleh-ka-unit'],
            ['noted_code' => '08', 'name' => 'Hold. Pekerjaan ditangguhkan karena ada pekerjaan lainnya yang lebih mendesak sementara sumber daya Fungsi Engineering tidak mencukupi', 'slug' => 'hold-pekerjaan-ditangguhkan'],
            ['noted_code' => '09', 'name' => 'Hold. Terdapat kebutuhan tambahan atau perubahan informasi yang belum bisa diperoleh dalam masa pelaksanaan pekerjaan', 'slug' => 'hold-terdapat-kebutuhan-tambahan'],
            ['noted_code' => '10', 'name' => 'Penyusunan dokumen DED', 'slug' => 'penyusunan-dokumen-ded'],
            ['noted_code' => '11', 'name' => 'Penyusunan dokumen Technical Assist', 'slug' => 'penyusunan-dokumen-technical-assist'],
            ['noted_code' => '12', 'name' => 'Penyusunan dokumen Kajian', 'slug' => 'penyusunan-dokumen-kajian'],
            ['noted_code' => '13', 'name' => 'Approval dokumen di e-DEMS', 'slug' => 'approval-dokumen-di-e-dems'],
            ['noted_code' => '14', 'name' => 'Approval dokumen di e-DEMS oleh Ka. Unit', 'slug' => 'approval-dokumen-di-e-dems-oleh-ka-unit'],
            ['noted_code' => '15', 'name' => 'Approval dokumen di e-DEMS oleh Ka. Dept.', 'slug' => 'approval-dokumen-di-e-dems-oleh-ka-dept'],
            ['noted_code' => '16', 'name' => 'Dokumen terkirim ke User melalui e-DEMS', 'slug' => 'dokumen-terkirim-ke-user-melalui-e-dems'],
            ['noted_code' => '17', 'name' => 'Cancel. ERF tidak sesuai dengan tupoksi Fungsi Engineering', 'slug' => 'cancel-erf-tidak-sesuai-dengan-tupoksi-fungsi-engineering'],
            ['noted_code' => '18', 'name' => 'Cancel. ERF tidak sesuai dengan kebijakan manajemen', 'slug' => 'cancel-erf-tidak-sesuai-dengan-kebijakan-manajemen'],
            ['noted_code' => '19', 'name' => 'Cancel. Terdapat ERF yang sama secara judul, latar belakang, maksud, tujuan dan ruang lingkup', 'slug' => 'cancel-terdapat-erf-yang-sama'],
            ['noted_code' => '20', 'name' => 'Cancel. Informasi yang diminta pada saat verifikasi masih belum dapat dilengkapi oleh Fungsi Peminta setelah 30 hari kalender atau setelah kesepakatan due date pada saat verifikasi', 'slug' => 'cancel-informasi-yang-diminta-belum-dilengkapi']
        ];

        foreach ($noted as $note) {
            Noted::create($note);
        }
    }
}
