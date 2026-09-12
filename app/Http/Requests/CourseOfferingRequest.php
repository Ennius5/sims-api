<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CourseOfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'academic_term_id' => ['required', 'exists:academic_terms,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'section' => ['required', 'string', 'max:20'],
            'schedule' => ['nullable', 'string', 'max:100'],
            'room' => ['nullable', 'string', 'max:50'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::in(['OPEN', 'CLOSED', 'CANCELLED'])],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $exists = \App\Models\CourseOffering::where('course_id', $this->course_id)
                ->where('academic_term_id', $this->academic_term_id)
                ->where('section', $this->section)
                ->when($this->route('course_offering'), fn ($q, $id) => $q->where('id', '!=', $id->id))
                ->exists();

            if ($exists) {
                $validator->errors()->add('section', 'This course/term/section combination already exists.');
            }
        });
    }
}
