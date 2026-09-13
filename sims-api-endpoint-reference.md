# SIMS API — Complete Endpoint Reference (Phases 1–5)

Base URL: `{{base_url}}` = `http://localhost:8000/api/v1`

All protected routes require header: `Authorization: Bearer <token>`

Roles: `administrator`, `registrar`, `instructor`, `student`

---

## Authentication

### Login
```
POST {{base_url}}/auth/login
```
Auth: none (public)
Body:
```json
{ "email": "admin@sims.test", "password": "password" }
```
Success `200`:
```json
{ "success": true, "message": "Login successful.", "data": { "user": {...}, "token": "1|xxxx" } }
```
Failure `422` — wrong password:
```json
{ "success": false, "message": "Validation failed.", "errors": { "email": ["The provided credentials are incorrect."] } }
```
Demo accounts (all password `password`): `admin@sims.test`, `registrar@sims.test`, `instructor1@sims.test`, `instructor2@sims.test`, `student1@sims.test`

### Logout
```
POST {{base_url}}/auth/logout
```
Auth: any authenticated user
No body.
Success `200`: `{ "success": true, "message": "Logged out successfully.", "data": null }`

### Current user
```
GET {{base_url}}/auth/me
```
Auth: any authenticated user
Success `200`: returns `id`, `name`, `email`, `roles`.
Failure `401` — no/invalid token: `{ "success": false, "message": "Unauthenticated." }`

---

## Programs
Auth: `administrator` or `registrar` only (role middleware). Any other role → `403`.

### List
```
GET {{base_url}}/programs?search=Info&status=ACTIVE&sort=name&per_page=10
```
Query params: `search` (partial match on name), `status`, `sort` (`name`, `code`, `created_at`; prefix `-` for descending), `per_page`.

### Create
```
POST {{base_url}}/programs
```
Body:
```json
{ "code": "BSIT", "name": "BS Information Technology", "description": "IT program", "status": "ACTIVE" }
```
Failure `422` — duplicate `code`.

### Retrieve
```
GET {{base_url}}/programs/{id}
```
Failure `404` for a bad id.

### Update
```
PUT {{base_url}}/programs/{id}
PATCH {{base_url}}/programs/{id}
```
Body: same shape as Create (PATCH accepts partial fields).

### Delete
```
DELETE {{base_url}}/programs/{id}
```
Success `200`, `data: null`.

---

## Courses
Auth: `administrator` or `registrar` only. Same shape as Programs.

### List
```
GET {{base_url}}/courses?search=intro&status=ACTIVE&sort=course_code&per_page=10
```

### Create
```
POST {{base_url}}/courses
```
Body:
```json
{ "course_code": "IT-101", "course_title": "Intro to IT", "description": "Foundations course", "units": 3, "status": "ACTIVE" }
```
Failure `422` — duplicate `course_code`, or `units` outside 1–10.

### Retrieve / Update / Delete
```
GET {{base_url}}/courses/{id}
PUT|PATCH {{base_url}}/courses/{id}
DELETE {{base_url}}/courses/{id}
```

---

## Academic Terms
Auth: `administrator` or `registrar` only.

### List
```
GET {{base_url}}/academic-terms?status=ACTIVE&sort=start_date&per_page=10
```

### Create
```
POST {{base_url}}/academic-terms
```
Body:
```json
{ "academic_year": "2026-2027", "semester": "1st", "start_date": "2026-08-01", "end_date": "2026-12-15", "status": "ACTIVE" }
```
Failure `422` — `end_date` before `start_date`, or duplicate `academic_year`+`semester` combo.

### Retrieve / Update / Delete
```
GET {{base_url}}/academic-terms/{id}
PUT|PATCH {{base_url}}/academic-terms/{id}
DELETE {{base_url}}/academic-terms/{id}
```

---

## Students
Auth: any authenticated user, but access is policy-gated (not role middleware):
- `index`, `create`, `update`, `delete` → admin/registrar (delete: admin only)
- `view` (show) → admin/registrar (any student), OR the logged-in student viewing **their own linked record**

### List
```
GET {{base_url}}/students?search=dela&program_id=1&year_level=3&status=ACTIVE&sort=last_name&per_page=20
```
Query params: `search` (matches first/last name or student number), `program_id`, `year_level`, `status`, `sort` (`last_name`, `first_name`, `student_number`, `created_at`).

### Create
```
POST {{base_url}}/students
```
Auth: admin/registrar only → `403` for others.
Body:
```json
{
  "student_number": "2026-99001",
  "first_name": "Test",
  "last_name": "Student",
  "program_id": 1,
  "year_level": 1,
  "email": "test.student@example.com"
}
```
Failure `422` — duplicate `student_number`, invalid `email`, or `program_id` not found.

### Retrieve
```
GET {{base_url}}/students/{id}
```
- As admin/registrar: any id → `200`
- As student, own linked id → `200`
- As student, someone else's id → `403`

### Update
```
PUT|PATCH {{base_url}}/students/{id}
```
Auth: admin/registrar only.

### Delete
```
DELETE {{base_url}}/students/{id}
```
Auth: administrator only.

---

## Course Offerings
Auth: any authenticated user (no extra role restriction currently applied at route or policy level).

### List
```
GET {{base_url}}/course-offerings?course_id=1&academic_term_id=1&instructor_id=3&status=OPEN&sort=section&per_page=20
```

### Create
```
POST {{base_url}}/course-offerings
```
Body:
```json
{
  "course_id": 1,
  "academic_term_id": 1,
  "instructor_id": 3,
  "section": "A",
  "schedule": "MWF 8:00-9:00",
  "room": "Rm 101",
  "capacity": 40,
  "status": "OPEN"
}
```
Failure `422` — duplicate `course_id`+`academic_term_id`+`section` combo, or invalid FK references.

### Retrieve / Update / Delete
```
GET {{base_url}}/course-offerings/{id}
PUT|PATCH {{base_url}}/course-offerings/{id}
DELETE {{base_url}}/course-offerings/{id}
```

### Students enrolled in an offering
```
GET {{base_url}}/course-offerings/{id}/students
```
Returns paginated enrollments (with student + program loaded) for that offering.

---

## Enrollments
Auth: any authenticated user, list results are scoped per role:
- admin/registrar → see all
- instructor → see only enrollments in offerings they teach
- student → see only their own enrollments
Create/update/delete restricted to admin/registrar via `EnrollmentPolicy`.

### List
```
GET {{base_url}}/enrollments?student_id=5&course_offering_id=3&status=ENROLLED&per_page=20
```

### Create
```
POST {{base_url}}/enrollments
```
Auth: admin/registrar only → `403` for others.
Body:
```json
{ "student_id": 5, "course_offering_id": 3, "enrollment_date": "2026-09-12" }
```
Failure `422` — duplicate `student_id`+`course_offering_id` pair (already enrolled), or invalid FK.

### Retrieve
```
GET {{base_url}}/enrollments/{id}
```
- admin/registrar → any
- instructor → only if they teach that offering
- student → only if it's their own enrollment
Otherwise `403`.

### Update
```
PATCH {{base_url}}/enrollments/{id}
```
Auth: admin/registrar only. (No `PUT` — spec allows GET/PATCH/DELETE only.)

### Delete
```
DELETE {{base_url}}/enrollments/{id}
```
Auth: admin/registrar only.

### A student's own enrollments
```
GET {{base_url}}/students/{id}/enrollments
```
Access follows `StudentPolicy` (same rule as viewing the student directly).

---

## Grades
Auth-sensitive: creating/updating a grade requires being admin/registrar, OR the instructor who teaches the offering tied to that enrollment.

### Create
```
POST {{base_url}}/grades
```
Body:
```json
{ "enrollment_id": 12, "midterm_grade": 1.75, "final_grade": 1.50, "remarks": "PASSED" }
```
Failure `422` — invalid `enrollment_id`, or grade out of range (1.0–5.0 in current scale — adjust to your actual grading scale if different).
Failure `403` — instructor attempting to grade an enrollment in an offering they don't teach.

### Retrieve
```
GET {{base_url}}/grades/{id}
```
Access follows `GradePolicy` (admin/registrar all, instructor own offerings only, student own grades only).

### Update
```
PUT|PATCH {{base_url}}/grades/{id}
```
Same access rule as create.

### A student's own grades
```
GET {{base_url}}/students/{id}/grades
```
Access follows `StudentPolicy`.

---

## Academic Record
```
GET {{base_url}}/students/{id}/academic-record
```
Access follows `StudentPolicy` (admin/registrar any student, student only their own).
Success `200` — response shape:
```json
{
  "success": true,
  "message": "Academic record retrieved successfully.",
  "data": {
    "student": { "id": 1, "student_number": "2026-78823", "name": "Cade Willms Considine" },
    "academic_record": {
      "2026-2027 - 1st": [
        { "course_code": "IT-101", "course_title": "Intro to IT", "units": 3, "midterm_grade": "1.75", "final_grade": "1.50", "remarks": "PASSED" }
      ]
    }
  }
}
```

---

## Quick reference: expected status codes to demo per resource

| Scenario | Status |
|---|---|
| Missing/invalid token on any protected route | 401 |
| Wrong role (e.g. student creating a program) | 403 |
| Nonexistent id (`GET /students/99999`) | 404 |
| Validation failure (bad email, missing field, out-of-range value) | 422 |
| Duplicate unique field (student_number, course_code, enrollment pair, offering combo) | 422 |
| Successful create | 201 |
| Successful read/update/list | 200 |
| Successful delete | 200 (with `data: null`) |

This table doubles as your checklist for the "show a 404", "show validation rejecting duplicate", and "show a forbidden request" items in the final demonstration.
