<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'course_offering_id' => ['required', 'exists:course_offerings,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['sometimes', Rule::in(['ENROLLED', 'DROPPED', 'COMPLETED'])],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $exists = \App\Models\Enrollment::where('student_id', $this->student_id)
                ->where('course_offering_id', $this->course_offering_id)
                ->when($this->route('enrollment'), fn ($q, $e) => $q->where('id', '!=', $e->id))
                ->exists();

            if ($exists) {
                $validator->errors()->add('course_offering_id', 'This student is already enrolled in this course offering.');
            }
        });
    }
}
