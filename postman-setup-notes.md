# Postman Setup Notes — SIMS API Batch Testing

## 1. Create an Environment

Environments hold variables that change per machine/run (base URL, tokens) so requests don't hardcode them.

1. Postman → **Environments** (left sidebar) → **+**
2. Name it `SIMS Local`
3. Add variables:

| Variable | Initial Value | Current Value |
|---|---|---|
| `base_url` | `http://localhost:8000/api/v1` | (same) |
| `admin_token` | (leave blank) | (leave blank) |
| `registrar_token` | (leave blank) | (leave blank) |
| `instructor_token` | (leave blank) | (leave blank) |
| `student_token` | (leave blank) | (leave blank) |

4. Select `SIMS Local` from the environment dropdown (top-right) so it's active.
5. From now on, write request URLs as `{{base_url}}/students` instead of the full hardcoded URL — this survives you changing ports, deploying, or running this on a different machine.

## 2. Auto-capture tokens with a Tests script

Instead of copy-pasting tokens after every login, have Postman save them automatically.

On each of your 4 login requests (admin, registrar, instructor, student), go to the **Scripts → Post-response** tab (older Postman calls this **Tests**) and add:

```javascript
const data = pm.response.json();
pm.environment.set("admin_token", data.data.token); // change variable name per request
```

Use a different variable name per account (`admin_token`, `student_token`, etc.) — either by having 4 separate saved login requests (one per role, each with its own Tests script line), or one login request where you manually swap the body + rerun for each role.

Then in every other request's **Authorization** tab, set:
- Type: `Bearer Token`
- Token: `{{admin_token}}` (or whichever role that request needs)

No more manual copy-pasting for the rest of the lab.

## 3. Organize your Collection by resource group

Create one Collection called `SIMS API`, with folders matching the spec's required groups:

```
SIMS API/
├── Authentication/       (login x4 roles, logout, me)
├── Programs/
├── Courses/
├── Academic Terms/
├── Students/
├── Course Offerings/
├── Enrollments/
├── Grades/
└── Academic Records/
```

Inside each folder, save both **success** and **failure** cases as separate saved requests — this matches the lab's requirement that your collection "includes both successful and failed requests":

- `Create Student - Valid`
- `Create Student - Duplicate Number (422)`
- `Create Student - Invalid Email (422)`
- `Get Student - Unauthorized Role (403)`
- `Get Student - Not Found (404)`

Save every request into its folder (Ctrl+S / Save, then pick the folder) as you build them out — don't leave them floating in history, since history isn't part of the exported collection.

## 4. Add basic assertions with pm.test()

For batch running to actually mean something (pass/fail, not just "did it not crash"), add assertions in each request's **Scripts → Post-response** tab:

```javascript
pm.test("Status is 201", () => pm.response.to.have.status(201));

pm.test("Response has success:true", () => {
    pm.expect(pm.response.json().success).to.be.true;
});
```

For an expected-failure request (e.g. duplicate student number):
```javascript
pm.test("Status is 422", () => pm.response.to.have.status(422));

pm.test("Has email/student_number error", () => {
    const body = pm.response.json();
    pm.expect(body.errors).to.have.property("student_number");
});
```

## 5. Batch-run with the Collection Runner

1. Click your `SIMS API` collection → **Run** (or the ▶ icon)
2. Select which folders/requests to include (or run the whole collection)
3. Set **Iterations**: 1 (unless using a data file — see below)
4. Make sure the `SIMS Local` environment is selected in the runner
5. Click **Run SIMS API**

Postman executes every selected request top-to-bottom, showing pass/fail per `pm.test()` assertion. This is your quickest way to re-verify the whole API still works after a change — run it after every phase from now on.

**Order matters** — put `Authentication/Login (Admin)` etc. at the top of the collection (or in its own folder run first) so the token variables are set before dependent requests run.

## 6. (Optional) Data-driven testing with a CSV/JSON file

For repeating the same request with different inputs (e.g. testing validation with 5 different bad emails), use a data file:

1. Create `test-data/invalid-students.csv`:
   ```csv
   student_number,email,expected_status
   2026-00001,not-an-email,422
   2026-00002,,201
   ```
2. In the request body, reference columns as `{{email}}`, `{{student_number}}`
3. In the Collection Runner, click **Select File** and choose your CSV
4. Set **Iterations** to match your row count (or "Automatic")

This isn't required by the lab, but it's a clean way to demonstrate thorough validation testing during your demo.

## 7. Export the collection + environment (required deliverable)

Once your folders are built out:

1. Collection → **⋯** → **Export** → Collection v2.1 → save as `SIMS-API.postman_collection.json`
2. Environments → `SIMS Local` → **⋯** → **Export** → save as `SIMS-Local.postman_environment.json`
3. **Before exporting, double-check no real secrets are baked into saved variable values** (e.g. don't leave a captured token as the environment's saved "Initial Value" — that's fine as "Current Value" locally, but Initial Value is what gets committed if you're not careful)
4. Include both files in your submission alongside the README, per the lab's "API Client Collection" deliverable requirement

## 8. (Optional, later) Automate via Newman

If you want to run the whole collection from the command line (e.g. as a pre-demo sanity check, or eventually in CI):

```bash
npm install -g newman
newman run SIMS-API.postman_collection.json -e SIMS-Local.postman_environment.json
```

This prints a full pass/fail report for every request — useful right before your final demonstration to confirm nothing regressed.
