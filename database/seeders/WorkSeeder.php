<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // It's recommended to run PlantSeeder and UserSeeder first.
        // Fetch related data to avoid querying inside the loop.
        $plants = \App\Models\Plant::pluck('id', 'name');
        $users = \App\Models\User::pluck('id', 'name');

        $faker = \Faker\Factory::create('id_ID');

        $works = [
            // Provided Data
            [
            'entry_date' => '2025-01-13', 'description' => 'Pembersihan Coating Dinding Silo 14', 'plant_name' => 'PPI', 'requester_unit' => 'PPI', 'work_priority' => null, 'work_type' => 'FEED/DED', 'request_category' => 'OPEX', 'erf_number' => 'PAD0/61/A3/EM/021/OE/2025', 'lead_engineer_name' => 'AULIA EKADANA FAUTHRISNO', 'verification_status' => 'Belum Verifikasi', 'project_status' => 'Not Started', 'current_phase' => 'Not started',
            ],
            [
            'entry_date' => '2025-01-06', 'description' => 'Penyaksian Opname Material Curah dan Kantong Semester II Periode 31 Desember 2024', 'plant_name' => 'GENERAL', 'requester_unit' => 'INTERNAL AUDIT', 'work_priority' => null, 'work_type' => 'FEED/DED', 'request_category' => 'OPEX', 'erf_number' => 'PAD0/10/A3/EM/010/OE/2025', 'erf_approved_date' => '2025-01-13', 'lead_engineer_name' => 'AULIA EKADANA FAUTHRISNO', 'initiating_target_date' => '2025-02-13', 'verification_status' => 'In Progress Verifikasi', 'project_status' => 'In Progress', 'current_phase' => 'Initiating',
            ],
            [
            'entry_date' => '2024-11-23', 'description' => 'Drawing Uji Siklik Dinding Precise Interlock Brick', 'plant_name' => 'BINS', 'requester_unit' => 'BISNIS INKUBASI NON SEMEN', 'work_priority' => null, 'work_type' => 'FEED/DED', 'request_category' => 'OPEX', 'erf_number' => 'PAD0/99/A3/EM/562/OE/2024', 'erf_approved_date' => '2024-11-25', 'clarification_date' => '2024-11-29', 'erf_validated_date' => '2024-12-06', 'lead_engineer_name' => 'AULIA EKADANA FAUTHRISNO', 'initiating_target_date' => '2024-12-25', 'executing_start_date' => '2025-01-13', 'executing_target_date' => '2025-02-11', 'verification_status' => 'Finish Verifikasi', 'project_status' => 'In Progress', 'current_phase' => 'Executing',
            ],
            [
            'entry_date' => '2024-04-19', 'description' => 'Modifikasi Hopper Unloading Klinker Teluk Bayur', 'plant_name' => 'PPTB', 'requester_unit' => 'UNIT OF PORT OPERATION & MAINTENANCE I', 'work_priority' => 'HIGH', 'work_type' => 'Kajian Engineering', 'request_category' => 'OPEX', 'erf_number' => 'PAD0/56/A2/EM/132/OE/2024', 'erf_approved_date' => '2024-04-19', 'lead_engineer_name' => 'NOVRIADI M', 'verification_status' => 'In Progress Verifikasi', 'executing_start_date' => null, 'project_status' => 'On Hold', 'current_phase' => 'Hold',
            ],
            [
            'entry_date' => '2024-11-19', 'description' => 'Stock Opname Limestone Bulan November 2024', 'plant_name' => 'GENERAL', 'requester_unit' => 'PRNCN &EVL PRD', 'work_priority' => null, 'work_type' => 'FEED/DED', 'request_category' => 'OPEX', 'erf_number' => 'PAD0/10/A3/EM/550/OE/2024', 'erf_approved_date' => '2024-12-12', 'clarification_date' => '2024-12-12', 'erf_validated_date' => '2024-12-12', 'lead_engineer_name' => 'AULIA EKADANA FAUTHRISNO', 'initiating_target_date' => '2025-01-12', 'executing_start_date' => '2025-01-08', 'executing_target_date' => '2025-01-28', 'executing_actual_date' => '2025-01-27', 'verification_status' => 'Finish Verifikasi', 'project_status' => 'Finish', 'current_phase' => 'Closing',
            ],
        ];

        // Generate additional data
        for ($i = 0; $i < 25; $i++) {
            $entryDate = $faker->dateTimeBetween('-1 year', '+6 months');
            $erfApprovedDate = (clone $entryDate)->modify('+'.rand(1, 10).' days');
            $executingStartDate = (clone $erfApprovedDate)->modify('+'.rand(10, 30).' days');
            $projectStatus = $faker->randomElement(['Not Started', 'In Progress', 'Finish', 'On Hold', 'Cancel']);

            $works[] = [
            'entry_date' => $entryDate->format('Y-m-d'),
            'description' => 'Kajian Teknis ' . $faker->sentence(3),
            'plant_name' => $faker->randomElement($plants->keys()),
            'requester_unit' => $faker->randomElement(['PPI', 'INTERNAL AUDIT', 'BINS', 'PPTB', 'PRNCN &EVL PRD']),
            'work_priority' => $faker->randomElement(['HIGH', 'MEDIUM']),
            'work_type' => $faker->randomElement(['FEED/DED', 'Kajian Engineering', 'Technical Assist']),
            'request_category' => $faker->randomElement(['CAPEX', 'OPEX']),
            'erf_number' => 'PAD0/'.rand(10,99).'/A3/EM/'.rand(100,999).'/OE/'.$entryDate->format('Y'),
            'lead_engineer_name' => $faker->randomElement($users->keys()),
            'erf_approved_date' => $projectStatus !== 'Not Started' ? $erfApprovedDate->format('Y-m-d') : null,
            'executing_start_date' => in_array($projectStatus, ['In Progress', 'Finish']) ? $executingStartDate->format('Y-m-d') : null,
            'executing_actual_date' => $projectStatus === 'Finish' ? (clone $executingStartDate)->modify('+'.rand(15, 40).' days')->format('Y-m-d') : null,
            'verification_status' => $faker->randomElement(['Belum Verifikasi', 'In Progress Verifikasi','Finish Verifikasi']),
            'project_status' => $projectStatus,
            'current_phase' => $faker->randomElement(['Not started', 'Initiating', 'Planning', 'Executing', 'Closing', 'Hold', 'Cancel']),
            ];
        }

        foreach ($works as $workData) {
            \App\Models\Work::create([
            'erf_number' => $workData['erf_number'] ?? null,
            'description' => $workData['description'],
            'entry_date' => $workData['entry_date'],
            'plant_id' => $plants[$workData['plant_name']] ?? null,
            'requester_unit' => $workData['requester_unit'] ?? null,
            'work_priority' => $workData['work_priority'] ?? null,
            'work_type' => $workData['work_type'] ?? null,
            'request_category' => $workData['request_category'] ?? null,
            'erf_approved_date' => $workData['erf_approved_date'] ?? null,
            'clarification_date' => $workData['clarification_date'] ?? null,
            'erf_validated_date' => $workData['erf_validated_date'] ?? null,
            'initiating_target_date' => $workData['initiating_target_date'] ?? null,
            'executing_start_date' => $workData['executing_start_date'] ?? null,
            'executing_target_date' => $workData['executing_target_date'] ?? null,
            'executing_actual_date' => $workData['executing_actual_date'] ?? null,
            'verification_status' => $workData['verification_status'] ?? null,
            'project_status' => $workData['project_status'] ?? null,
            'current_phase' => $workData['current_phase'] ?? null,
            'lead_engineer_id' => $users[$workData['lead_engineer_name']] ?? null,
            'slug' => \Illuminate\Support\Str::slug($workData['description'] . '-' . ($workData['erf_number'] ?? uniqid())),
            ]);
        }
    }
}
