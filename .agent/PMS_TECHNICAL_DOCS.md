# 🛠️ Performance Management System (PMS) - Technical Documentation

## 1. System Architecture
The PMS is implemented as a sub-module within the **HRM Module**, following the project's modular architecture.

- **Base Directory:** `app/Modules/HRM`
- **Namespace:** `App\Modules\HRM`
- **Frontend:** Blade templates with Bootstrap 5, utilizing **Offcanvas** for CRUD operations and **AJAX** for form submissions.

## 2. Database Schema
The system introduces 11 new tables handling relationships for performance tracking.

### Core Tables
1. **`hrm_kpis`**: Stores KPIs/KRAs definitions.
   - `department_id`: Optional link for department-level KPIs.
   - `type`: Enum ('kpi', 'kra').
2. **`hrm_competencies`**: reusable behavioral skills.
   - `type`: 'core', 'functional', 'leadership'.

### Goal Tracking Tables
3. **`hrm_okrs`**: Objectives container.
   - `level`: 'company', 'department', 'individual'.
   - `progress`: Calculated aggregate of key results.
4. **`hrm_okr_key_results`**: Specific metrics for OKRs.
   - `current_value` / `target_value`: Used to calculate percentage automatically.
5. **`hrm_performance_goals`**: Individual tasks/goals.
   - Linked to `hrm_employees` and optionally `hrm_kpis`.

### Appraisal Tables
6. **`hrm_360_appraisals`**: The main appraisal instance.
   - `review_period`: e.g., "Q1 2024".
7. **`hrm_appraisal_reviewers`**: Pivot table connecting Appraisal <-> Employee (Reviewer).
   - `reviewer_type`: 'peer', 'manager', 'self', etc.
   - `rating`: Overall rating from this reviewer.
8. **`hrm_appraisal_competency_ratings`**: Granular scores per competency.
   - Linked to `hrm_appraisal_reviewers` and `hrm_competencies`.

### PIP Tables
9. **`hrm_pips`**: Performance Improvement Plans.
10. **`hrm_pip_action_items`**: Specific tasks within a PIP.
11. **`hrm_pip_reviews`**: Progress logs/reviews for a PIP.

## 3. Key Relationships (ERD Concept)
- **Employee** has many **PerformanceGoals**.
- **Employee** has many **OKRs**.
- **OKR** has many **KeyResults**.
- **Employee** has many **Appraisals** (as subject).
- **Appraisal** has many **Reviewers** (Employees providing feedback).
- **Reviewer** entry has many **CompetencyRatings**.

## 4. Code Implementation Details

### Models
Located in `app/Modules/HRM/Models`. Models use standard Eloquent relationships and Casts.
- **Progress Calculation**: Models like `Okr` and `Pip` have methods like `getCompletionPercentage()` to dynamically calculate status based on children relations (KeyResults or ActionItems).

### Controllers
Located in `app/Modules/HRM/Http/Controllers`.
- **AJAX Driven**: Most `store` and `update` methods return JSON responses to work with the offcanvas forms.
- **Permission Middleware**: All controllers use `middleware('permission:hrm.performance.view')` etc., enforced via Spatie Permissions.

### Views
Located in `app/Modules/HRM/resources/views/pages/performance`.
- **Structure**:
  - `index.blade.php`: Main list + Create/Edit Offcanvas forms.
  - `show.blade.php`: Detailed view (for OKRs, Appraisals, PIPs).
- **Layout Extension**: Views extend `Main::layouts.app` to integrate with the core application shell.

### Routes
Defined in `app/Modules/HRM/routes/web.php`.
- Prefix: `hrm/`
- Grouped under `auth` middleware.
- Resourceful routes used where possible (index, store, update, destroy).

## 5. Setup & Seeding
A unified seeder `PMSSeeder` is available to populate the system with:
- Required Permissions.
- Sample Competencies.
- Demo KPIs, OKRs, and Appraisals.

**Run Seeder:**
```bash
php artisan db:seed --class=App\\Modules\\HRM\\Database\\Seeders\\PMSSeeder
```

## 6. Permissions List
The system uses the following permissions (Guard: `web`, Module: `HRM`):
- `hrm.performance.view`
- `hrm.performance.create`
- `hrm.performance.edit`
- `hrm.performance.delete`
- `hrm.pips.view` (Specific for sensitive PIP data)
- `hrm.pips.create`
- `hrm.pips.edit`
- `hrm.pips.delete`

## 7. Future Extensibility
- **Automated Scheduling**: Add a cron job to automatically change OKR status based on dates.
- **Notification System**: Trigger email/system notifications when:
  - An appraisal is assigned.
  - A goal due date is approaching.
  - A PIP is initiated.
- **Analytics Dashboard**: Create a visualization widget for the main dashboard showing "Average Dept Competency Score" or "OKR Completion Rates".
