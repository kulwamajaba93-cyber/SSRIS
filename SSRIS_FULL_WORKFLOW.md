# SSRIS Full System Workflow

## Student Supervisor Research Interaction System (SSRIS)

---

## Table of Contents
1. [System Overview](#system-overview)
2. [Technology Stack](#technology-stack)
3. [User Roles](#user-roles)
4. [Step-by-Step Workflows](#step-by-step-workflows)
5. [Research Lifecycle Stages](#research-lifecycle-stages)
6. [Notifications and Alerts](#notifications-and-alerts)
7. [Models & Relationships](#models-and-relationships)
8. [File References](#file-references)

---

## System Overview
SSRIS is a Laravel-based web application designed to manage student-supervisor research interactions at Moshi Cooperative University (MoCU). The system centralizes document management, meeting scheduling, feedback, and communication, with SMS notifications via NextSMS API.

---

## Technology Stack
- **Backend**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: MySQL (using `ssris_users`, `proposals`, `meetings`, `feedback`, `sms_logs`, `meeting_student`, etc.)
- **Frontend**: Bootstrap 5, Blade Templates, JavaScript
- **SMS Integration**: NextSMS API v2
- **File Storage**: Laravel Public Storage
- **Architecture**: MVC (Model-View-Controller)

---

## User Roles
1. **Administrator**: Manages users, assigns supervisors, views reports, configures system
2. **Supervisor**: Reviews documents, provides feedback, manages meetings, approves stages
3. **Student**: Submits documents, receives feedback, views meetings, tracks progress

---

## Step-by-Step Workflows

### 1. Administrator Workflow
- **Initial Setup**:
  1. Login to Admin Dashboard (route: `admin.dashboard`)
  2. Create user accounts (students, supervisors) via `admin.users.create`
  3. Bulk import students via CSV (route: `admin.users.import-csv`)
  4. Assign supervisors to students via `admin.assign-supervisor`
  5. View interaction tracking via `admin.interaction-tracking.index`
  6. Approve/Reject supervisor performance
  7. Reset user passwords if necessary

### 2. Supervisor Workflow
- **Dashboard (route: `supervisor.dashboard`)**:
  1. View assigned students list
  2. Check pending document reviews
  3. View upcoming meetings
- **Document Review**:
  1. Navigate to `supervisor.proposals.index`
  2. Download document via `supervisor.proposals.download`
  3. Submit feedback using `supervisor.feedback.create`
  4. Document statuses: `pending`, `approved`, `revision`
- **Meeting Management**:
  1. Schedule new meetings (students can be single or multiple) via `supervisor.meetings.create`
  2. Record completed meetings with discussion notes and action points
  3. Use checkbox select for multi-student meetings
- **Messaging**:
  1. Communicate with students via in-app messaging (route: `supervisor.messages.index`)

### 3. Student Workflow
- **Dashboard (route: `student.dashboard`)**:
  1. View current research stage and progress
  2. Check recent submissions and pending feedback
- **Document Submission**:
  1. Submit Concept Note, Proposal, Data Collection, or Final Report via `student.documents.create`
  2. Auto-incrementing version numbers for resubmissions
  3. File types: PDF, DOC, DOCX (max size: 10MB)
- **Meetings**:
  1. View scheduled and recorded meetings at `student.meetings.index`
  2. Join Google Meet sessions via provided link
- **Messaging**:
  1. Send/receive messages with supervisor via `student.messages.index`

---

## Research Lifecycle Stages
SSRIS enforces a sequential research workflow (only proceed to next stage after current stage is approved):
1. **Concept Note**: Student submits → Supervisor approves/rejects
2. **Proposal**: Student submits → Supervisor reviews/approves/rejects
3. **Data Collection & Analysis**: Student submits report → Supervisor approves
4. **Final Report**: Student submits → Supervisor approves
5. **Completed**: Final report approved → Research marked complete

---

## Notifications and Alerts
SSRIS uses NextSMS API for SMS notifications:
1. **Student Submits Document**: Supervisor receives SMS
2. **Supervisor Sends Feedback**: Student receives SMS
3. **Meeting Scheduled**: All selected students receive SMS
4. **Recorded Meetings**: No SMS notifications are sent
5. **SMS Logging**: All SMS attempts are logged to the `sms_logs` table

---

## Models and Relationships
Key models and their relationships:
### `User` Model (`app/Models/User.php`)
Base user model, uses `ssris_users` table:
- `supervisor()`: BelongsTo User
- `assignedStudents()`: HasMany User (supervisor's students)
- `proposals()`: HasMany Proposal
- `feedbackReceived()`: HasMany Feedback
- `meetings()`: HasMany Meeting (backwards compatibility)
- `meetingParticipations()`: BelongsToMany Meeting (via `meeting_student` pivot table)
- `sentMessages()`: HasMany Message
- `receivedMessages()`: HasMany Message

### `Proposal` Model (`app/Models/Proposal.php`)
Stores research document submissions (file: `app/Models/Proposal.php`):
- `student()`: BelongsTo User
- Document types: `concept_notes`, `proposal`, `data_collection`, `report`
- Statuses: `pending`, `approved`, `revision`

### `Meeting` Model (`app/Models/Meeting.php`)
Stores scheduled/recorded meetings:
- `supervisor()`: BelongsTo User
- `student()`: BelongsTo User (single student, for backwards compatibility)
- `students()`: BelongsToMany User (via `meeting_student`)
- Statuses: `scheduled`, `completed`

---

## File References
Key application files and directories (with clickable links):
- **Routes**: [routes/web.php](file:///c:/laragon/www/ssris/routes/web.php) → Defines all URL endpoints and middleware
- **Controllers**: `app/Http/Controllers/`
  - Admin Controllers: `Admin/`
  - Supervisor Controllers: `Supervisor/`
  - Student Controllers: `Student/`
- **Models**: `app/Models/`
  - [User.php](file:///c:/laragon/www/ssris/app/Models/User.php)
  - [Proposal.php](file:///c:/laragon/www/ssris/app/Models/Proposal.php)
  - [Meeting.php](file:///c:/laragon/www/ssris/app/Models/Meeting.php)
  - [Feedback.php](file:///c:/laragon/www/ssris/app/Models/Feedback.php)
  - [SmsLog.php](file:///c:/laragon/www/ssris/app/Models/SmsLog.php)
- **Services**: [app/Services/SmsService.php](file:///c:/laragon/www/ssris/app/Services/SmsService.php) → SMS sending logic
- **Views**: `resources/views/`
  - Admin Views: `admin/`
  - Supervisor Views: `supervisor/`
  - Student Views: `student/`
- **Migrations**: `database/migrations/` → Database table definitions
- **Configuration**: `.env` → NextSMS credentials, DB settings, etc.

---

## Example Database Table Structures
### `ssris_users` Table (Users)
Stores all users (admin, supervisor, student):
- `id`: Primary key
- `name`: Full user name
- `username`: Registration number (for students only)
- `email`: Email address
- `password`: Bcrypt-hashed password
- `role`: `admin`, `supervisor`, or `student`
- `phone`: Phone number for SMS notifications (normalized to `255XXXXXXXXX` format)
- `program`: Student program (e.g. BBICT, BBAF)
- `reg_number`: Short registration number
- `year`: Admission year
- `supervisor_id`: Foreign key (supervisor assigned to student)
- `department`: Supervisor department
- `is_approved`: Supervisor approval status
- `timestamps`: created_at, updated_at

### `proposals` Table (Documents)
Stores research document submissions:
- `id`: Primary key
- `student_id`: Foreign key (User)
- `document_type`: `concept_notes`, `proposal`, `data_collection`, `report`
- `title`: Document title
- `file_path`: Path to uploaded file in storage
- `version`: Auto-incrementing document version number
- `status`: `pending`, `approved`, `revision`
- `notes`: Submission notes
- `timestamps`: created_at, updated_at

### `meetings` Table (Meetings)
Stores scheduled/recorded meetings:
- `id`: Primary key
- `supervisor_id`: Foreign key (User)
- `student_id`: Foreign key (User, for single-student backwards compatibility)
- `title`: Meeting title
- `meeting_date`: Date and time of meeting
- `meeting_url`: Google Meet URL (nullable)
- `discussion_notes`: Notes from recorded meetings
- `action_points`: Action items for students
- `status`: `scheduled`, `completed`
- `timestamps`: created_at, updated_at

