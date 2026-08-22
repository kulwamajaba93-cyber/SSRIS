# SSRIS Workflow - For Drawing Framework/Diagrams
## Student Supervisor Research Interaction System (SSRIS)
---

## 1. SYSTEM OVERVIEW
SSRIS is a Laravel-based web application that manages the entire undergraduate research supervision lifecycle at Moshi Co-operative University (MoCU).

---

## 2. USER ROLES & ACCESS LEVELS
| Role | Responsibilities |
|------|------------------|
| **Administrator** | Create/manage users, assign supervisors, approve supervisor performance, view reports, manage system settings |
| **Supervisor** | Review student documents, provide feedback, schedule meetings, approve research stages, communicate with students |
| **Student** | Submit research documents, view feedback, track research progress, communicate with supervisor, view meetings |

---

## 3. RESEARCH LIFECYCLE (CORE WORKFLOW)
### 3.1 Research Stages (Sequential, Must Be Approved to Move Forward)
| Step Number | Stage Name | Description |
|-------------|------------|-------------|
| 1 | Concept Notes | Student submits initial research idea |
| 2 | Proposal | Student submits full research proposal |
| 3 | Data Collection & Analysis | Student submits data collection plan/results |
| 4 | Report | Student submits final research report |
| 5 | Completed | All stages approved → Research finished |

### 3.2 Document Types & Corresponding Stages
- `concept_notes` → Stage 1
- `proposal` → Stage 2
- `data_collection` → Stage 3
- `report` → Stage 4

### 3.3 Document Statuses
1. `pending` - Document submitted, waiting for review
2. `under_review` - Supervisor is reviewing the document
3. `reviewed` - Supervisor has reviewed (no decision yet)
4. `approved` - Document approved → Student proceeds to next stage
5. `rejected` - Document rejected
6. `revision` - Revision required → Student must resubmit

---

## 4. STEP-BY-STEP WORKFLOW
### 4.1 Initial Setup (Admin)
1. Admin creates user accounts (students, supervisors) OR imports via CSV
2. Admin assigns supervisors to students
3. Admin approves supervisor accounts if needed

### 4.2 Student Research Journey
For each stage (1 to 4):
   a. **Student submits document** (with version number auto-incrementing) → This is the first step to start a stage
   b. System sends SMS notification to supervisor
   c. Supervisor downloads and reviews document
   d. **(Optional) Supervisor schedules/records a meeting** (can happen before/after document submission or feedback)
   e. Supervisor provides feedback (with priority, due date) OR
   f. Supervisor updates document status (approved/rejected/revision)
   g. System sends SMS notification to student
   h. If revision required: Student revises and resubmits → Repeat b-g
   i. If approved: Student moves to next research stage

**Key Note**: Submitting a document is the required first step to progress through a research stage. Meetings are supplementary and can be scheduled at any time (before/after document submission or feedback).

### 4.3 Meeting Management
1. Supervisor schedules a meeting (can select single or multiple students)
2. System sends SMS notification to selected students
3. Supervisor records meeting notes/action points (no SMS sent for recorded meetings)
4. Students can view scheduled/recorded meetings

---

## 5. SYSTEM COMPONENTS (FOR ARCHITECTURE DIAGRAMS)
### 5.1 Frontend
- Blade Templates
- Bootstrap 5
- Tailwind CSS
- JavaScript

### 5.2 Backend
- Laravel 12.x (PHP 8.2+)
- MVC Architecture
- Controllers:
  - Admin Controllers (Dashboard, UserManagement, Report, InteractionTracking)
  - Supervisor Controllers (Dashboard, Feedback, Meeting, ResearchStage, Message)
  - Student Controllers (Dashboard, Feedback, Document, Message, Meeting)
- Middleware: `auth`, `admin`, `supervisor`, `student`
- Services: `SmsService` (NextSMS API integration)

### 5.3 Database (MySQL)
| Table Name | Purpose |
|------------|---------|
| `users` | Stores all users (admin, supervisor, student) |
| `research_stages` | Defines the 5 research stages |
| `student_progress` | Tracks individual student progress through stages |
| `research_projects` | Manages student research projects |
| `proposals` | Stores submitted research documents (all types) |
| `feedback` | Stores supervisor feedback on documents |
| `meetings` | Stores scheduled/recorded meetings |
| `meeting_student` | Pivot table for multi-student meetings |
| `messages` | Stores user-to-user messages |
| `interactions` | Tracks user interactions |
| `sms_logs` | Logs all SMS notifications sent |

### 5.4 External Integrations
- **NextSMS API**: Sends automated SMS notifications

---

## 6. DATA FLOW
### 6.1 Document Submission Flow
1. Student → Submits document via `student.documents.store`
2. System → Saves document to `storage/app/`
3. System → Creates `proposals` table record (with version number)
4. System → Calls `SmsService` to send SMS to supervisor
5. System → Logs SMS in `sms_logs` table

### 6.2 Feedback/Approval Flow
1. Supervisor → Submits feedback or updates status via supervisor routes
2. System → Saves feedback to `feedback` table OR updates `proposals` status
3. System → Calls `SmsService` to send SMS to student
4. System → Logs SMS in `sms_logs` table
5. If approved → System updates `student_progress` to next stage

---

## 7. KEY NOTES FOR DIAGRAMS
- **Role-Based Access Control (RBAC)**: All routes protected by role middleware
- **Sequential Workflow**: Cannot move to next stage without approval of current stage
- **SMS Notifications**: Sent for document submissions, feedback, approvals, and scheduled meetings (NOT for recorded meetings)
- **Version Control**: Documents have auto-incrementing version numbers
