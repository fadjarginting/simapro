<?php

// File: app/Http/Requests/StoreWorkRequest.php

namespace App\Http\Requests;

use App\Enums\WorkPriority;
use App\Enums\WorkType;
use App\Enums\RequestCategory;
use App\Enums\VerificationStatus;
use App\Enums\ProjectStatus;
use App\Enums\CurrentPhase;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Atau sesuaikan dengan authorization logic Anda
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'erf_number'             => 'nullable|string|max:255|unique:works,erf_number',
            'description'            => 'required|string|min:5|max:1000',
            'plant_id'               => 'required|exists:plants,id',
            'requester_unit'         => 'required|string|max:255',
            'work_priority'          => 'required|string|in:' . $this->getEnumValues(WorkPriority::cases()),
            'work_type'              => 'required|string|in:' . $this->getEnumValues(WorkType::cases()),
            'request_category'       => 'required|string|in:' . $this->getEnumValues(RequestCategory::cases()),
            'entry_date'             => 'required|date',
            'erf_approved_date'      => 'nullable|date|after_or_equal:entry_date',
            'clarification_date'     => 'nullable|date|after_or_equal:erf_approved_date',
            'erf_validated_date'     => 'nullable|date|after_or_equal:clarification_date',
            'initiating_target_date' => 'nullable|date|after_or_equal:erf_validated_date',
            'executing_start_date'   => 'nullable|date',
            'executing_target_date'  => 'nullable|date|after_or_equal:executing_start_date',
            'executing_actual_date'  => 'nullable|date',
            'verification_status'    => 'nullable|string|in:' . $this->getEnumValues(VerificationStatus::cases()),
            'project_status'         => 'nullable|string|in:' . $this->getEnumValues(ProjectStatus::cases()),
            'current_phase'          => 'nullable|string|in:' . $this->getEnumValues(CurrentPhase::cases()),
            'lead_engineer_id'       => 'nullable|exists:users,id',
            'note_id'                => 'nullable|exists:noted,id'
        ];
    }

    /**
     * Dapatkan pesan validasi kustom.
     */
    public function messages(): array
    {
        return [
            'erf_number.unique'               => 'Nomor ERF sudah digunakan.',
            'description.required'            => 'Deskripsi pekerjaan wajib diisi.',
            'description.string'              => 'Deskripsi pekerjaan harus berupa teks.',
            'description.min'                 => 'Deskripsi pekerjaan minimal 5 karakter.',
            'description.max'                 => 'Deskripsi pekerjaan maksimal 1000 karakter.',
            'plant_id.required'               => 'Plant wajib dipilih.',
            'plant_id.exists'                 => 'Plant yang dipilih tidak valid.',
            'requester_unit.required'         => 'Unit kerja peminta wajib diisi.',
            'requester_unit.string'           => 'Unit kerja peminta harus berupa teks.',
            'requester_unit.max'              => 'Unit kerja peminta maksimal 255 karakter.',
            'work_priority.required'          => 'Prioritas pekerjaan wajib dipilih.',
            'work_priority.string'            => 'Prioritas pekerjaan harus berupa teks.',
            'work_priority.in'                => 'Prioritas pekerjaan yang dipilih tidak valid.',
            'work_type.required'              => 'Jenis pekerjaan wajib dipilih.',
            'work_type.string'                => 'Jenis pekerjaan harus berupa teks.',
            'work_type.in'                    => 'Jenis pekerjaan yang dipilih tidak valid.',
            'request_category.required'       => 'Kategori permintaan wajib dipilih.',
            'request_category.string'         => 'Kategori permintaan harus berupa teks.',
            'request_category.in'             => 'Kategori permintaan yang dipilih tidak valid.',
            'entry_date.required'             => 'Tanggal masuk wajib diisi.',
            'entry_date.date'                 => 'Format tanggal masuk tidak valid.',
            'erf_approved_date.date'          => 'Format tanggal persetujuan ERF tidak valid.',
            'erf_approved_date.after_or_equal' => 'Tanggal persetujuan ERF tidak boleh sebelum tanggal masuk.',
            'clarification_date.date'         => 'Format tanggal klarifikasi tidak valid.',
            'clarification_date.after_or_equal' => 'Tanggal klarifikasi tidak boleh sebelum tanggal persetujuan ERF.',
            'erf_validated_date.date'         => 'Format tanggal validasi ERF tidak valid.',
            'erf_validated_date.after_or_equal' => 'Tanggal validasi ERF tidak boleh sebelum tanggal klarifikasi.',
            'initiating_target_date.date'     => 'Format tanggal target inisiasi tidak valid.',
            'initiating_target_date.after_or_equal' => 'Tanggal target inisiasi tidak boleh sebelum tanggal validasi ERF.',
            'executing_start_date.date'       => 'Format tanggal mulai eksekusi tidak valid.',
            'executing_target_date.date'      => 'Format tanggal target eksekusi tidak valid.',
            'executing_target_date.after_or_equal' => 'Tanggal target eksekusi tidak boleh sebelum tanggal mulai eksekusi.',
            'executing_actual_date.date'      => 'Format tanggal eksekusi aktual tidak valid.',
            'verification_status.string'      => 'Status verifikasi harus berupa teks.',
            'verification_status.in'          => 'Status verifikasi yang dipilih tidak valid.',
            'project_status.string'           => 'Status proyek harus berupa teks.',
            'project_status.in'               => 'Status proyek yang dipilih tidak valid.',
            'current_phase.string'            => 'Fase saat ini harus berupa teks.',
            'current_phase.in'                => 'Fase saat ini yang dipilih tidak valid.',
            'lead_engineer_id.exists'         => 'Lead engineer yang dipilih tidak valid.',
            'note_id.exists'                  => 'Catatan yang dipilih tidak valid.'
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'erf_number' => 'nomor ERF',
            'description' => 'deskripsi pekerjaan',
            'plant_id' => 'plant',
            'requester_unit' => 'unit peminta',
            'work_priority' => 'prioritas pekerjaan',
            'work_type' => 'jenis pekerjaan',
            'request_category' => 'kategori permintaan',
            'entry_date' => 'tanggal masuk',
            'erf_approved_date' => 'tanggal persetujuan ERF',
            'clarification_date' => 'tanggal klarifikasi',
            'lead_engineer_id' => 'lead engineer',
            'note_id' => 'catatan',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        session()->flash('error', 'Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.');
        parent::failedValidation($validator);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Bersihkan data sebelum validasi jika diperlukan
        if ($this->has('erf_number') && empty($this->erf_number)) {
            $this->merge(['erf_number' => null]);
        }
    }

    /**
     * Get comma-separated enum values for validation.
     */
    private function getEnumValues(array $enumCases): string
    {
        return implode(',', array_column($enumCases, 'value'));
    }
}