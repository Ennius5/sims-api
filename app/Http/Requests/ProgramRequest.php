<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate handled at route/policy level
    }

    public function rules(): array
    {
        $programId = $this->route('program')?->id;

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('programs', 'code')->ignore($programId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }
}
