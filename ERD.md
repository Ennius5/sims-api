# SIMS API — Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| STUDENTS : "has one (optional)"
    USERS ||--o{ COURSE_OFFERINGS : "teaches (instructor)"
    PROGRAMS ||--o{ STUDENTS : "has many"
    STUDENTS ||--o{ ENROLLMENTS : "has many"
    COURSES ||--o{ COURSE_OFFERINGS : "has many"
    ACADEMIC_TERMS ||--o{ COURSE_OFFERINGS : "has many"
    COURSE_OFFERINGS ||--o{ ENROLLMENTS : "has many"
    ENROLLMENTS ||--o| GRADES : "has one"

    USERS {
        bigint id PK
        string name
        string email
        string password_hash
        string status
        timestamp created_at
        timestamp updated_at
    }

    STUDENTS {
        bigint id PK
        bigint user_id FK "nullable, unique"
        string student_number "unique"
        string first_name
        string middle_name
        string last_name
        string suffix
        date birth_date
        string email
        string contact_number
        string address
        bigint program_id FK
        tinyint year_level
        string status
        timestamp created_at
        timestamp updated_at
    }

    PROGRAMS {
        bigint id PK
        string code "unique"
        string name
        text description
        string status
        timestamp created_at
        timestamp updated_at
    }

    COURSES {
        bigint id PK
        string course_code "unique"
        string course_title
        text description
        tinyint units
        string status
        timestamp created_at
        timestamp updated_at
    }

    ACADEMIC_TERMS {
        bigint id PK
        string academic_year
        string semester
        date start_date
        date end_date
        string status
        timestamp created_at
        timestamp updated_at
    }

    COURSE_OFFERINGS {
        bigint id PK
        bigint course_id FK
        bigint academic_term_id FK
        bigint instructor_id FK "nullable, references users"
        string section
        string schedule
        string room
        int capacity
        string status
        timestamp created_at
        timestamp updated_at
    }

    ENROLLMENTS {
        bigint id PK
        bigint student_id FK
        bigint course_offering_id FK
        date enrollment_date
        string status
        timestamp created_at
        timestamp updated_at
    }

    GRADES {
        bigint id PK
        bigint enrollment_id FK "unique"
        decimal midterm_grade
        decimal final_grade
        string remarks
        timestamp created_at
        timestamp updated_at
    }
```

## Key constraints
- `students.student_number` — unique
- `courses.course_code` — unique
- `students.user_id` — unique (one login account maps to at most one student profile)
- `academic_terms(academic_year, semester)` — unique combination
- `course_offerings(course_id, academic_term_id, section)` — unique combination (prevents duplicate section offerings)
- `enrollments(student_id, course_offering_id)` — unique combination (prevents duplicate enrollment)
- `grades.enrollment_id` — unique (one grade record per enrollment; retakes require a separate enrollment)

## Roles (via spatie/laravel-permission, not a separate table in this diagram)
`administrator`, `registrar`, `instructor`, `student` — assigned to `users` via the package's pivot tables (`model_has_roles`, `roles`, `permissions`).
