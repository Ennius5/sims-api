# Student Information Management REST API (SIMS API)

Backend-only REST API for managing student records, academic programs, courses, terms, course offerings, enrollments, and grades — built for the AI-Assisted Framework-Based REST API Development laboratory activity.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2)
- **Auth:** Laravel Sanctum (token-based)
- **Roles/Permissions:** spatie/laravel-permission
- **Search/Filter/Sort/Pagination:** spatie/laravel-query-builder
- **API Docs:** dedoc/scramble (OpenAPI/Swagger)
- **Testing:** Pest (+ pest-plugin-laravel)
- **Database:** MySQL (via XAMPP for local development)

## Prerequisites

- PHP 8.2+
- Composer
- MySQL (XAMPP or standalone)
- Postman, Insomnia, or equivalent API client

## Installation

```bash
git clone <repo-url>
cd sims-api
composer install
cp .env.example .env
php artisan key:generate
```

## Environment Configuration

Edit `.env` with your local database credentials:

```
APP_ENV=local
APP_PORT=8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sims_api
DB_USERNAME=root
DB_PASSWORD=
```

## Database Setup

Create the database (via phpMyAdmin or CLI):
```bash
mysql -u root -e "CREATE DATABASE sims_api"
```

## Migrations & Seeding

```bash
php artisan migrate:fresh --seed
```

This runs all migrations and seeds:
- 4 roles: `administrator`, `registrar`, `instructor`, `student`
- 5 demo user accounts (see **Authentication** below)
- 3 academic programs (BSIT, BSCS, BSIS)
- 100 students
- 20 courses
- 2 academic terms
- 20 course offerings
- 200 enrollments
- 100 grades

## Running the API

```bash
php artisan serve
```

API base URL: `http://localhost:8000/api/v1`

## Authentication

All endpoints except `POST /auth/login` require a Bearer token, obtained via login.

**Demo accounts** (all use password `password`):

| Role | Email |
|---|---|
| Administrator | admin@sims.test |
| Registrar | registrar@sims.test |
| Instructor | instructor1@sims.test |
| Instructor | instructor2@sims.test |
| Student | student1@sims.test |

Login:
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@sims.test","password":"password"}'
```

Response includes a `data.token` — pass it as `Authorization: Bearer <token>` on subsequent requests.

Get current user: `GET /api/v1/auth/me`
Logout (revoke token): `POST /api/v1/auth/logout`

## API Documentation

Interactive OpenAPI/Swagger docs (via Scramble), available once the server is running:
```
http://localhost:8000/docs/api
```

A full manual endpoint reference (with example requests/responses for every route) is also included in this repo — see `docs/endpoint-reference.md`.

## API Client Collection

A Postman collection and environment are included:
- `postman/SIMS-API.postman_collection.json`
- `postman/SIMS-Local.postman_environment.json`

Import both into Postman, select the `SIMS Local` environment, and run the collection via the Collection Runner to execute the full request suite (success and failure cases for every resource group).

## Running Tests

```bash
php artisan test
```

Covers authentication, student CRUD/validation, authorization (role and object-level), enrollment duplicate-prevention, grade authorization, and collection search/filter/sort/pagination.

## Entity Relationship Diagram

See `docs/ERD.md` — Mermaid-format ERD covering all 8 core tables, relationships, and unique constraints.

## AI-Assisted Development Summary

This project was built with AI assistance (Claude) used for: architecture and package selection, migration/model/controller/policy scaffolding, debugging (including several real issues: Windows Defender file-lock during Composer installs, incorrect route-group nesting causing unintended public/role-restricted endpoints, seeder ordering causing role-dependent factory failures, and a missing Eloquent relationship causing object-level authorization to silently fail), and documentation drafting.

Every AI-generated suggestion was manually reviewed, tested via `php artisan route:list -v`, Tinker, and Postman before being accepted, and multiple bugs surfaced during that verification process were traced to their root cause and fixed rather than worked around (e.g. the `students` routes briefly inheriting an unintended `role:administrator|registrar` middleware group; several endpoints briefly running outside the `auth:sanctum` group entirely). The developer can explain and modify all architecture, security, and business-logic decisions in this codebase.

## Project Structure Notes

- All API controllers live under `app/Http/Controllers/Api/V1/`
- Authorization is enforced via a mix of route-level role middleware (Programs/Courses/Academic Terms — admin/registrar only) and model Policies (Students, Enrollments, Grades — supports finer-grained, object-level rules like "a student may only view their own record")
- All list endpoints support `search`, relevant `filter[...]` fields, `sort`, and `per_page` (capped at 100) via `spatie/laravel-query-builder`
- All API responses follow a consistent `{success, message, data}` / `{success, message, errors}` shape, including for framework-level exceptions (401/403/404/422/400) via custom exception rendering in `bootstrap/app.php`
