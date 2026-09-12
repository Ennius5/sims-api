<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'midterm_grade' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'final_grade' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'remarks' => ['nullable', 'string', 'max:20'],
        ];
    }
}
