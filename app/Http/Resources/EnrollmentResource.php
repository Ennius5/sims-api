<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student' => new StudentResource($this->whenLoaded('student')),
            'course_offering' => new CourseOfferingResource($this->whenLoaded('courseOffering')),
            'enrollment_date' => $this->enrollment_date?->format('Y-m-d'),
            'status' => $this->status,
            'grade' => new GradeResource($this->whenLoaded('grade')),
            'created_at' => $this->created_at,
        ];
    }
}
