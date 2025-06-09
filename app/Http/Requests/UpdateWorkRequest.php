<?php

namespace App\Http\Requests;

use App\Enums\WorkType;
use App\Enums\CurrentPhase;
use App\Enums\WorkPriority;
use App\Enums\ProjectStatus;
use App\Enums\RequestCategory;
use Illuminate\Validation\Rule;
use App\Enums\VerificationStatus;
use Illuminate\Foundation\Http\FormRequest;


class UpdateWorkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'erf_number'             => ['nullable', 'string', 'max:255', Rule::unique('works', 'erf_number')->ignore($this->work)],
            'description'            => 'sometimes|string|min:5|max:1000',
            'plant_id'               => 'sometimes|exists:plants,id',
            'requester_unit'         => 'sometimes|string|max:255',
            'work_priority'          => 'sometimes|string|in:' . $this->getEnumValues(WorkPriority::cases()),
            'work_type'              => 'sometimes|string|in:' . $this->getEnumValues(WorkType::cases()),
            'request_category'       => 'sometimes|string|in:' . $this->getEnumValues(RequestCategory::cases()),
            'verification_status'    => 'nullable|string|in:' . $this->getEnumValues(VerificationStatus::cases()),
            'project_status'         => 'nullable|string|in:' . $this->getEnumValues(ProjectStatus::cases()),
            'current_phase'          => 'nullable|string|in:' . $this->getEnumValues(CurrentPhase::cases()),
            'lead_engineer_id'       => 'nullable|exists:users,id',
            'note_id'                => 'nullable|exists:noted,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'erf_number.unique'       => 'Nomor ERF sudah digunakan.',
            'description.string'      => 'Deskripsi pekerjaan harus berupa teks.',
            'description.min'         => 'Deskripsi pekerjaan minimal 5 karakter.',
            'description.max'         => 'Deskripsi pekerjaan maksimal 1000 karakter.',
            'plant_id.exists'         => 'Plant yang dipilih tidak valid.',
            'lead_engineer_id.exists' => 'Lead engineer yang dipilih tidak valid.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'erf_number'        => 'nomor ERF',
            'description'       => 'deskripsi pekerjaan',
            'plant_id'          => 'plant',
            'requester_unit'    => 'unit peminta',
            'work_priority'     => 'prioritas pekerjaan',
            'work_type'         => 'jenis pekerjaan',
            'request_category'  => 'kategori permintaan',
            'lead_engineer_id'  => 'lead engineer',
            'note_id'           => 'catatan',
        ];
    }

    /**
     * Get comma-separated enum values for validation.
     */
    private function getEnumValues(array $enumCases): string
    {
        return implode(',', array_column($enumCases, 'value'));
    }
}