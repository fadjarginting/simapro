<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'plant_id' => 'nullable|integer|exists:plants,id',
            'work_priority' => 'nullable|string',
            'work_type' => 'nullable|string',
            'request_category' => 'nullable|string',
            'verification_status' => 'nullable|string',
            'project_status' => 'nullable|string',
            'current_phase' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_start_date' => 'nullable|date',
            'target_end_date' => 'nullable|date|after_or_equal:target_start_date',
            'sort_by' => 'nullable|string|in:erf_number,description,entry_date,work_priority,work_type,project_status,current_phase,executing_target_date',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function getFilters(): array
    {
        return $this->only([
            'search', 'plant_id', 'work_priority', 'work_type', 'request_category',
            'verification_status', 'project_status', 'current_phase',
            'start_date', 'end_date', 'target_start_date', 'target_end_date',
            'sort_by', 'sort_order', 'per_page'
        ]);
    }

    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'per_page' => min($this->get('per_page', 10), 100),
            'sort_by' => $this->get('sort_by', 'erf_number'),
            'sort_order' => $this->get('sort_order', 'desc'),
        ]);
    }
}