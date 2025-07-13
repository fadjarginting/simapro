<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEatRequest extends FormRequest
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
            'work_id' => 'required|exists:works,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activities' => 'required|array|min:1',
            'activities.*.activity_name' => 'required|string|max:255',
            'activities.*.discipline_id' => 'required|exists:disciplines,id',
            'activities.*.activity_description' => 'nullable|string|max:1000',
            'activities.*.start_date' => 'required|date',
            'activities.*.end_date' => 'required|date|after_or_equal:activities.*.start_date',
            'activities.*.pic_ids' => 'required|array|min:1',
            'activities.*.pic_ids.*' => 'exists:users,id',
            'approvals' => 'required|array|min:1',
            'approvals.*.approver_id' => 'required|exists:users,id',
            'isDraft' => 'boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'work_id.required' => 'Work ID wajib diisi.',
            'work_id.exists' => 'Work ID tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa format tanggal yang valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai harus berupa format tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'activities.required' => 'Minimal harus ada satu aktivitas.',
            'activities.array' => 'Format aktivitas tidak valid.',
            'activities.min' => 'Minimal harus ada satu aktivitas.',
            'activities.*.activity_name.required' => 'Nama aktivitas wajib diisi.',
            'activities.*.activity_name.string' => 'Nama aktivitas harus berupa teks.',
            'activities.*.activity_name.max' => 'Nama aktivitas tidak boleh lebih dari 255 karakter.',
            'activities.*.discipline_id.required' => 'Disiplin aktivitas wajib diisi.',
            'activities.*.discipline_id.exists' => 'Disiplin aktivitas tidak valid.',
            'activities.*.activity_description.string' => 'Deskripsi aktivitas harus berupa teks.',
            'activities.*.activity_description.max' => 'Deskripsi aktivitas tidak boleh lebih dari 1000 karakter.',
            'activities.*.start_time.required' => 'Waktu mulai aktivitas wajib diisi.',
            'activities.*.start_time.date' => 'Waktu mulai aktivitas harus  berupa format tanggal yang valid.',
            'activities.*.end_time.required' => 'Waktu selesai aktivitas wajib diisi.',
            'activities.*.end_time.date' => 'Waktu selesai aktivitas harus berupa format tanggal yang valid.',
            'activities.*.end_time.after_or_equal' => 'Waktu selesai aktivitas tidak boleh sebelum waktu mulai aktivitas.',
            'activities.*.pic_ids.required' => 'Minimal harus ada satu PIC untuk setiap aktivitas.',
            'activities.*.pic_ids.array' => 'Format PIC tidak valid.',
            'activities.*.pic_ids.*.exists' => 'PIC tidak valid.',
            'approvals.required' => 'Minimal harus ada satu persetujuan.',
            'approvals.array' => 'Format persetujuan tidak valid.',
            'approvals.min' => 'Minimal harus ada satu persetujuan.',
            'approvals.*.approver_id.required' => 'ID persetujuan wajib diisi.',
            'approvals.*.approver_id.exists' => 'ID persetujuan tidak valid.',
            'isDraft.boolean' => 'Status draft harus berupa boolean.',
        ];
    }   

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure activities and approvals are arrays
        $this->merge([
            'activities' => $this->activities ?? [],
            'approvals' => $this->approvals ?? [],
            'isDraft' => $this->isDraft ?? false,
        ]);
        // Convert start_date and end_date to date format
        if ($this->start_date) {
            $this->merge(['start_date' => date('Y-m-d', strtotime($this->start_date))]);
        }
        if ($this->end_date) {
            $this->merge(['end_date' => date('Y-m-d', strtotime($this->end_date))]);
        }   
        // Convert activity start_time and end_time to date format
        foreach ($this->activities as $key => $activity) {
            if (isset($activity['start_time'])) {
                $this->merge([
                    "activities.$key.start_time" => date('Y-m-d H:i:s', strtotime($activity['start_time']))
                ]);
            }
            if (isset($activity['end_time'])) {
                $this->merge([
                    "activities.$key.end_time" => date('Y-m-d H:i:s', strtotime($activity['end_time']))
                ]);
            }
        }
        // Convert approval dates to date format if needed
        foreach ($this->approvals as $key => $approval) {
            if (isset($approval['approval_date'])) {
                $this->merge([
                    "approvals.$key.approval_date" => date('Y-m-d', strtotime($approval['approval_date']))
                ]);
            }
        }
    }     
}