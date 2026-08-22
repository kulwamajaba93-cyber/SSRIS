# SSRIS - Student-Supervisor Research Interaction System
## Project Framework Documentation

---

## 📋 TABLE OF CONTENTS

1. [System Overview](#system-overview)
2. [Architecture Diagram](#architecture-diagram)
3. [Technology Stack](#technology-stack)
4. [System Features by User Role](#system-features-by-user-role)
5. [Database Schema](#database-schema)
6. [User Roles & Permissions](#user-roles--permissions)
7. [Workflow Processes](#workflow-processes)
8. [File Structure](#file-structure)
9. [API Endpoints](#api-endpoints)
10. [Security Features](#security-features)
11. [Deployment Guide](#deployment-guide)

---

## 🎯 SYSTEM OVERVIEW

**SSRIS** is a comprehensive web-based research management system designed for MoCU (Moshi Co-operative University) to facilitate interaction between students and supervisors throughout the research process.

### **Core Objectives:**
- Streamline student-supervisor communication
- Track research progress through defined stages
- Manage document submissions and feedback
- Schedule and track meetings
- Provide real-time notifications

### **Key Features:**
- Multi-role authentication (Admin, Supervisor, Student)
- Document management with version control
- Research progress tracking
- Meeting scheduling and management
- Real-time messaging system
- SMS notifications
- Performance approval workflow

---

## 🏗️ ARCHITECTURE DIAGRAM

```
┌─────────────────────────────────────────────────────────────────┐
│                        SSRIS SYSTEM ARCHITECTURE                   │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   FRONTEND   │    │  BACKEND     │    │  DATABASE    │
│              │    │              │    │              │
│  - Blade     │◄──►│  Laravel     │◄──►│   MySQL      │
│  - Bootstrap │    │  Controllers │    │              │
│  - jQuery    │    │  Models      │    │  Tables:     │
│  - FontAwesome│   │  Middleware │    │  - ssris_users│
└──────────────┘    └──────────────┘    │  - proposals │
                    ┌──────────────┐    │  - meetings  │
                    │  SERVICES    │    │  - feedback  │
                    │              │    │  - messages  │
                    │  - SMS Service│   │  - research_ │
                    │  - Auth       │    │    projects  │
                    │  - Validation │    │  - research_ │
                    └──────────────┘    │    stages    │
                                         └──────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                        USER ROLES & ACCESS                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   ADMIN     │    │ SUPERVISOR  │    │   STUDENT   │
├─────────────┤    ├─────────────┤    ├─────────────┤
│ ✓ User Mgmt │    │ ✓ View Students│   │ ✓ Submit Docs│
│ ✓ Assign    │    │ ✓ Review Docs  │   │ ✓ View Feedback│
│   Supervisors│   │ ✓ Give Feedback│   │ ✓ Schedule Mtgs│
│ ✓ Approve   │    │ ✓ Schedule Mtgs│   │ ✓ Track Progress│
│   Supervisors│   │ ✓ Track Progress│ │ ✓ Messages   │
│ ✓ System    │    │ ✓ Messages     │   │ ✓ View Dashboard│
│   Settings  │    │ ✓ View Dashboard│ │              │
└─────────────┘    └─────────────┘    └─────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    RESEARCH WORKFLOW STAGES                       │
└─────────────────────────────────────────────────────────────────┘

┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│ CONCEPT   │──►│ PROPOSAL │──►│ DATA     │──►│ REPORT   │
│ NOTES     │   │          │   │ COLLECTION│   │          │
│ (Stage 0) │   │ (Stage 1)│   │ (Stage 2)│   │ (Stage 3)│
└──────────┘   └──────────┘   └──────────┘   └──────────┘
     │              │              │              │
     ▼              ▼              ▼              ▼
  Submit        Submit        Submit        Submit
     │              │              │              │
     ▼              ▼              ▼              ▼
  Review        Review        Review        Review
     │              │              │              │
     ▼              ▼              ▼              ▼
  Approve/      Approve/      Approve/      Approve/
  Reject        Reject        Reject        Reject
```

---

## 🛠️ TECHNOLOGY STACK

### **Backend:**
- **Framework:** Laravel 12.58.0
- **PHP Version:** 8.2.4
- **Database:** MySQL
- **ORM:** Eloquent

### **Frontend:**
- **Template Engine:** Blade
- **CSS Framework:** Bootstrap 5.3.0
- **JavaScript:** jQuery
- **Icons:** FontAwesome 6.0.0
- **Charts:** Chart.js (if needed)

### **Services:**
- **SMS:** Custom SMS Service
- **Email:** Laravel Mail
- **File Storage:** Local (public disk)
- **Session:** File-based
- **Cache:** File-based

### **Development Tools:**
- **Composer:** PHP Package Manager
- **Artisan:** Laravel CLI
- **Git:** Version Control

---

## 🎯 SYSTEM FEATURES BY USER ROLE

### **4.1.5 System Features Overview**

The SSRIS system implements a comprehensive set of features organized by user role to ensure efficient research management and communication. Each role (Administrator, Supervisor, Student) has access to specific functionalities tailored to their responsibilities in the research process.

---

### **ADMINISTRATOR FEATURES**

#### **Authentication & Security**
- Login/logout functionality with secure session management
- Role-based access control with custom middleware
- "Remember me" functionality for persistent sessions
- Password protection through Laravel's Bcrypt hashing
- Full system administration access and oversight

#### **User Management System**
- Create, edit, and delete user accounts
- Bulk student creation and import functionality
- Supervisor assignment to students
- User profile management and updates
- Password reset functionality
- User activity monitoring and audit trails

#### **System Administration & Analytics**
- System overview and statistics dashboard
- Supervisor performance reports and analytics
- Document submission analytics and tracking
- Meeting and feedback tracking across all users
- Export capabilities for reports and data
- System configuration management

#### **Performance Approval Workflow**
- HOD-level supervisor performance approval
- Performance metrics review and approval
- Performance report generation
- Approval workflow management
- Performance tracking and reporting

#### **Administrative Oversight**
- View all system data and activities
- Monitor document submissions across all students
- Track meeting schedules and attendance
- Review feedback history and quality
- Generate comprehensive system reports
- System health monitoring

---

### **SUPERVISOR FEATURES**

#### **Authentication & Security**
- Login/logout functionality with secure session management
- Role-based access control with custom middleware
- "Remember me" functionality for persistent sessions
- Password protection through Laravel's Bcrypt hashing
- Secure access to assigned student data only

#### **Document Management System**
- Review and approve/reject student document submissions
- Provide structured feedback on concept notes, proposals, data collection reports, and final reports
- View document version history and tracking
- Download and review submitted documents
- Track document status (submitted, under review, approved, rejected)
- Access document submission history per student

#### **Meeting Management System**
- Schedule meetings with assigned students
- Set meeting dates, times, and locations
- Integrate Google Meet URLs for virtual meetings
- Support multi-student meeting scheduling with checkbox selection
- Record meeting discussions, notes, and action points
- Track meeting history and attendance
- View upcoming and past meetings

#### **Feedback System**
- Provide structured feedback on student submissions
- Track feedback status (pending, reviewed, approved, rejected)
- View feedback history and revisions
- Update feedback based on student revisions
- Monitor feedback response times

#### **Progress Tracking Dashboard**
- Visual dashboard showing assigned students' research stage progress
- Track individual student progress through research stages
- Monitor document submission statistics per student
- View meeting attendance and participation
- Calculate completion rates for assigned students
- Performance metrics and analytics

#### **Communication System**
- Send and receive messages with assigned students
- Real-time messaging system
- Message history tracking
- Notification system for new messages

#### **SMS Notification System**
- Receive automated SMS notifications for document submissions
- Get notified when students submit new documents
- Meeting reminder SMS notifications
- SMS logging and tracking

#### **Dashboard & Analytics**
- Role-specific supervisor dashboard
- View assigned students list and their status
- Performance metrics and statistics
- Document review queue management
- Meeting scheduling overview
- Feedback pending notifications

---

### **STUDENT FEATURES**

#### **Authentication & Security**
- Login/logout functionality with secure session management
- Role-based access control with custom middleware
- "Remember me" functionality for persistent sessions
- Password protection through Laravel's Bcrypt hashing
- Secure access to personal research data only

#### **Document Management System**
- Electronic submission of concept notes, proposals, data collection reports, and final reports
- File upload with validation and secure storage
- Document versioning and history tracking
- View document submission status (submitted, under review, approved, rejected)
- Download approved and reviewed documents
- Delete unreviewed documents
- Upload revised versions based on feedback

#### **Meeting Management System**
- Request and schedule meetings with assigned supervisor
- View scheduled meeting details (date, time, location, Google Meet URL)
- Access meeting history and past meeting records
- View meeting notes and action points
- Receive meeting reminders and notifications
- Track meeting attendance

#### **Feedback System**
- View supervisor feedback on submitted documents
- Track feedback status and response requirements
- Access feedback history for all submissions
- Revise documents based on supervisor feedback
- Receive notifications when feedback is provided

#### **Progress Tracking Dashboard**
- Visual dashboard showing personal research stage progress
- Track progress through research stages (Concept Notes → Proposal → Data Collection → Report)
- View document submission history and status
- Monitor meeting attendance and participation
- Calculate personal completion rate
- View research timeline and milestones

#### **Communication System**
- Send and receive messages with assigned supervisor
- Real-time messaging system
- Message history tracking
- Notification system for new messages

#### **SMS Notification System**
- Receive automated SMS notifications when feedback is provided
- Get meeting reminder SMS notifications
- Document submission confirmation SMS
- SMS notification logging

#### **Dashboard & Personal Management**
- Role-specific student dashboard
- View personal research progress overview
- Access document submission queue
- View upcoming and past meetings
- Check pending feedback and notifications
- Personal profile management

---

### **SHARED SYSTEM FEATURES**

#### **User Interface**
- Responsive design using Bootstrap 5.3
- Role-specific dashboards and layouts
- Intuitive navigation menus
- Form validation and error handling
- Loading states and user feedback
- Mobile-friendly interface

#### **Notification System**
- Real-time in-app notifications
- SMS notifications integration with NextSMS API
- Email notifications (optional)
- Notification history and tracking

#### **File Management**
- Secure file upload and storage
- File validation and type checking
- Version control for documents
- Download functionality
- File access control by role

#### **Data Security**
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- CSRF protection
- Secure session management
- HttpOnly and SameSite cookies

---

## 🗄️ DATABASE SCHEMA

### **Core Tables:**

#### **1. ssris_users**
```sql
- id (Primary Key)
- name (String)
- username (String, Nullable, Unique) - For students
- email (String, Nullable, Unique) - For supervisors/admin
- phone (String, Nullable)
- role (Enum: admin, supervisor, student)
- password (String - Hashed)
- program (Enum: BBICT, BHRM, BAT - Nullable)
- reg_number (String - Registration number, Nullable)
- year (String - Academic year, Nullable)
- supervisor_id (Foreign Key - Self-referencing)
- is_approved (Boolean)
- department (String, Nullable)
- performance_approval_status (String, Nullable)
- performance_signed_at (Timestamp, Nullable)
- performance_hod_remarks (Text, Nullable)
- performance_approved_by (Foreign Key, Nullable)
- remember_token (String, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **2. proposals**
```sql
- id (Primary Key)
- project_id (Foreign Key - research_projects, Nullable)
- student_id (Foreign Key - ssris_users)
- title (String)
- abstract (Text, Nullable)
- file_path (String, Nullable)
- original_filename (String, Nullable)
- version (Integer - Default: 1)
- document_type (Enum: concept_notes, proposal, data_collection, report)
- status (Enum: pending, under_review, reviewed, approved, rejected, revision)
- submission_notes (Text, Nullable)
- submitted_at (Timestamp, Nullable)
- reviewed_at (Timestamp, Nullable)
- reviewed_by (Foreign Key - ssris_users, Nullable)
- review_comments (Text, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **3. meetings**
```sql
- id (Primary Key)
- project_id (Foreign Key - research_projects, Nullable)
- student_id (Foreign Key - ssris_users)
- supervisor_id (Foreign Key - ssris_users)
- title (String)
- description (Text, Nullable)
- meeting_date (DateTime)
- meeting_time (Time)
- location (String, Nullable)
- status (Enum: scheduled, completed, cancelled)
- notes (Text, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **4. feedback**
```sql
- id (Primary Key)
- proposal_id (Foreign Key - proposals)
- student_id (Foreign Key - ssris_users)
- supervisor_id (Foreign Key - ssris_users)
- comments (Text)
- status (Enum: pending, reviewed)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **5. messages**
```sql
- id (Primary Key)
- sender_id (Foreign Key - ssris_users)
- receiver_id (Foreign Key - ssris_users)
- message (Text)
- is_read (Boolean - Default: false)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **6. research_projects**
```sql
- id (Primary Key)
- student_id (Foreign Key - ssris_users)
- supervisor_id (Foreign Key - ssris_users)
- title (String)
- description (Text, Nullable)
- status (Enum: ongoing, completed, suspended)
- start_date (Date, Nullable)
- end_date (Date, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **7. research_stages**
```sql
- id (Primary Key)
- name (String)
- description (Text, Nullable)
- step_number (Integer)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **8. student_progress**
```sql
- id (Primary Key)
- student_id (Foreign Key - ssris_users)
- stage_id (Foreign Key - research_stages)
- status (Enum: pending, in_progress, completed, approved, rejected)
- approved_by (Foreign Key - ssris_users, Nullable)
- approved_at (Timestamp, Nullable)
- comments (Text, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

#### **9. sms_logs**
```sql
- id (Primary Key)
- phone_number (String)
- message (Text)
- status (Enum: sent, failed, pending)
- response (Text, Nullable)
- sent_at (Timestamp, Nullable)
- created_at (Timestamp)
- updated_at (Timestamp)
```

---

## 👥 USER ROLES & PERMISSIONS

### **ADMINISTRATOR**

#### **Authentication & Security Features**
- Login/logout functionality with secure session management
- Role-based access control with custom middleware
- "Remember me" functionality for persistent sessions
- Password protection through Laravel's Bcrypt hashing
- Full system administration access

#### **User Management**
- Create, edit, and delete user accounts
- Bulk student creation and import functionality
- Supervisor assignment to students
- User profile management and updates
- Password reset functionality
- User activity monitoring

#### **System Administration**
- System overview and statistics dashboard
- Supervisor performance reports and analytics
- Document submission analytics and tracking
- Meeting and feedback tracking across all users
- Export capabilities for reports and data
- System configuration management

#### **Performance Approval**
- HOD-level supervisor performance approval
- Performance metrics review and approval
- Performance report generation
- Approval workflow management
- Performance tracking and reporting

#### **Administrative Oversight**
- View all system data and activities
- Monitor document submissions across all students
- Track meeting schedules and attendance
- Review feedback history and quality
- Generate comprehensive system reports
- System health monitoring

---

### **SUPERVISOR**

#### **Authentication & Security Features**
- Login/logout functionality with secure session management
- Role-based access control with custom middleware
- "Remember me" functionality for persistent sessions
- Password protection through Laravel's Bcrypt hashing
- Secure access to assigned student data only

#### **Document Management**
- Review and approve/reject student document submissions
- Provide structured feedback on concept notes, proposals, data collection reports, and final reports
- View document version history and tracking
- Download and review submitted documents
- Track document status (submitted, under review, approved, rejected)
- Access document submission history per student

#### **Meeting Management**
- Schedule meetings with assigned students
- Set meeting dates, times, and locations
- Integrate Google Meet URLs for virtual meetings
- Support multi-student meeting scheduling with checkbox selection
- Record meeting discussions, notes, and action points
- Track meeting history and attendance
- View upcoming and past meetings

#### **Feedback System**
- Provide structured feedback on student submissions
- Track feedback status (pending, reviewed, approved, rejected)
- View feedback history and revisions
- Update feedback based on student revisions
- Monitor feedback response times

#### **Progress Tracking**
- Visual dashboard showing assigned students' research stage progress
- Track individual student progress through research stages
- Monitor document submission statistics per student
- View meeting attendance and participation
- Calculate completion rates for assigned students
- Performance metrics and analytics

#### **Communication**
- Send and receive messages with assigned students
- Real-time messaging system
- Message history tracking
- Notification system for new messages

#### **SMS Notifications**
- Receive automated SMS notifications for document submissions
- Get notified when students submit new documents
- Meeting reminder SMS notifications
- SMS logging and tracking

#### **Dashboard & Analytics**
- Role-specific supervisor dashboard
- View assigned students list and their status
- Performance metrics and statistics
- Document review queue management
- Meeting scheduling overview
- Feedback pending notifications

---

### **STUDENT**

#### **Authentication & Security Features**
- Login/logout functionality with secure session management
- Role-based access control with custom middleware
- "Remember me" functionality for persistent sessions
- Password protection through Laravel's Bcrypt hashing
- Secure access to personal research data only

#### **Document Management**
- Electronic submission of concept notes, proposals, data collection reports, and final reports
- File upload with validation and secure storage
- Document versioning and history tracking
- View document submission status (submitted, under review, approved, rejected)
- Download approved and reviewed documents
- Delete unreviewed documents
- Upload revised versions based on feedback

#### **Meeting Management**
- Request and schedule meetings with assigned supervisor
- View scheduled meeting details (date, time, location, Google Meet URL)
- Access meeting history and past meeting records
- View meeting notes and action points
- Receive meeting reminders and notifications
- Track meeting attendance

#### **Feedback System**
- View supervisor feedback on submitted documents
- Track feedback status and response requirements
- Access feedback history for all submissions
- Revise documents based on supervisor feedback
- Receive notifications when feedback is provided

#### **Progress Tracking**
- Visual dashboard showing personal research stage progress
- Track progress through research stages (Concept Notes → Proposal → Data Collection → Report)
- View document submission history and status
- Monitor meeting attendance and participation
- Calculate personal completion rate
- View research timeline and milestones

#### **Communication**
- Send and receive messages with assigned supervisor
- Real-time messaging system
- Message history tracking
- Notification system for new messages

#### **SMS Notifications**
- Receive automated SMS notifications when feedback is provided
- Get meeting reminder SMS notifications
- Document submission confirmation SMS
- SMS notification logging

#### **Dashboard & Personal Management**
- Role-specific student dashboard
- View personal research progress overview
- Access document submission queue
- View upcoming and past meetings
- Check pending feedback and notifications
- Personal profile management

---

## 🔄 WORKFLOW PROCESSES

### **Document Submission Workflow:**

```
1. Student submits document
   ↓
2. System validates and stores document
   ↓
3. Supervisor receives notification (SMS + System)
   ↓
4. Supervisor reviews document
   ↓
5. Supervisor provides feedback
   ↓
6. Student receives notification
   ↓
7. Student makes revisions (if required)
   ↓
8. Document approved → Progress to next stage
```

### **Research Progress Tracking:**

```
Stage 0: Concept Notes
  ↓ Submit → Review → Approve
Stage 1: Proposal
  ↓ Submit → Review → Approve
Stage 2: Data Collection & Analysis
  ↓ Submit → Review → Approve
Stage 3: Report
  ↓ Submit → Review → Approve
Final: Research Complete
```

### **Meeting Scheduling:**

```
1. Student/Supervisor requests meeting
   ↓
2. Other party accepts/proposes alternative time
   ↓
3. Meeting scheduled
   ↓
4. Both parties receive notification
   ↓
5. Meeting conducted
   ↓
6. Meeting notes recorded
   ↓
7. Status updated to completed
```

---

## 📁 FILE STRUCTURE

```
ssris/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── UserManagementController.php
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisteredController.php
│   │   │   ├── Student/
│   │   │   │   ├── DocumentController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── MeetingController.php
│   │   │   │   └── MessageController.php
│   │   │   ├── Supervisor/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── FeedbackController.php
│   │   │   │   ├── MeetingController.php
│   │   │   │   └── MessageController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── StudentMiddleware.php
│   │   │   └── SupervisorMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Proposal.php
│   │   ├── Meeting.php
│   │   ├── Feedback.php
│   │   ├── Message.php
│   │   ├── ResearchProject.php
│   │   ├── ResearchStage.php
│   │   ├── StudentProgress.php
│   │   └── SmsLog.php
│   └── Services/
│       └── SmsService.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   └── session.php
├── database/
│   ├── migrations/
│   │   ├── 2026_05_03_062314_create_ssris_users_table.php
│   │   ├── 2026_05_03_080658_create_research_projects_table.php
│   │   ├── 2026_05_03_080659_create_proposals_table.php
│   │   ├── 2026_05_03_080712_create_meetings_table.php
│   │   ├── 2026_05_03_080714_create_feedback_table.php
│   │   ├── 2026_05_06_140000_create_research_stages_table.php
│   │   ├── 2026_05_06_140400_create_messages_table.php
│   │   ├── 2026_07_14_132430_create_student_progress_table.php
│   │   └── ...
│   └── seeders/
├── public/
│   ├── storage/
│   │   └── proposals/ (Document uploads)
│   └── index.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── admin.blade.php
│   │   │   ├── student.blade.php
│   │   │   └── supervisor.blade.php
│   │   ├── auth/
│   │   │   └── login.blade.php
│   │   ├── admin/
│   │   │   └── users/
│   │   ├── student/
│   │   │   ├── documents/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── meetings/
│   │   │   └── messages/
│   │   └── supervisor/
│   │       ├── proposals/
│   │       ├── dashboard.blade.php
│   │       ├── meetings/
│   │       └── messages/
│   └── js/
│       └── bootstrap.js
├── routes/
│   └── web.php
├── storage/
│   ├── framework/
│   │   ├── sessions/
│   │   └── cache/
│   └── logs/
├── tests/
├── .env
├── .env.example
├── artisan
├── composer.json
└── SSRIS_PROJECT_FRAMEWORK.md (This file)
```

---

## 🔌 API ENDPOINTS

### **Authentication Routes:**
- `GET /login` - Show login form
- `POST /login` - Process login
- `POST /logout` - Process logout

### **Admin Routes:**
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/users` - User management
- `POST /admin/users` - Create user
- `POST /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Delete user
- `POST /admin/users/assign-supervisor` - Assign supervisor

### **Student Routes:**
- `GET /student/dashboard` - Student dashboard
- `GET /student/documents` - Document list
- `POST /student/documents` - Submit document
- `GET /student/documents/{id}` - View document
- `DELETE /student/documents/{id}` - Delete document
- `GET /student/meetings` - Meeting list
- `POST /student/meetings` - Schedule meeting
- `GET /student/messages` - Message list
- `POST /student/messages` - Send message

### **Supervisor Routes:**
- `GET /supervisor/dashboard` - Supervisor dashboard
- `GET /supervisor/proposals` - Document review list
- `GET /supervisor/proposals/{id}` - View document
- `POST /supervisor/feedback` - Provide feedback
- `GET /supervisor/meetings` - Meeting list
- `POST /supervisor/meetings` - Schedule meeting
- `GET /supervisor/messages` - Message list
- `POST /supervisor/messages` - Send message

---

## 🔒 SECURITY FEATURES

### **Authentication:**
- Laravel Authentication System
- Session-based authentication
- Remember me functionality
- CSRF protection
- Password hashing (Bcrypt)

### **Authorization:**
- Role-based access control (RBAC)
- Middleware for each role
- Route protection
- User ownership verification

### **Data Security:**
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- File upload validation
- Secure file storage
- HttpOnly cookies
- SameSite cookie protection

### **Session Security:**
- Secure session configuration
- Session regeneration on login
- Session timeout management
- CSRF token validation

---

## 🚀 DEPLOYMENT GUIDE

### **Development Setup:**
```bash
# Clone repository
git clone <repository-url>
cd ssris

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
```

### **Production Deployment:**

#### **Server Requirements:**
- PHP >= 8.2
- MySQL >= 5.7
- Composer
- Web Server (Apache/Nginx)
- SSL Certificate

#### **Deployment Steps:**
```bash
# 1. Upload files to server
# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 4. Configure environment
nano .env
# Update APP_ENV=production
# Update APP_DEBUG=false
# Update database credentials
# Update APP_URL

# 5. Generate application key
php artisan key:generate

# 6. Run migrations
php artisan migrate --force

# 7. Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Configure web server
# Point document root to /public
```

#### **Web Server Configuration:**

**Apache VirtualHost:**
```apache
<VirtualHost *:80>
    ServerName ssris.mocu.ac.tz
    DocumentRoot /var/www/ssris/public
    
    <Directory /var/www/ssris/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ssris-error.log
    CustomLog ${APACHE_LOG_DIR}/ssris-access.log combined
</VirtualHost>
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name ssris.mocu.ac.tz;
    root /var/www/ssris/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📊 SYSTEM MONITORING

### **Log Files:**
- Application logs: `storage/logs/laravel.log`
- Error logs: Server error logs
- Access logs: Server access logs

### **Performance Monitoring:**
- Database query optimization
- Cache utilization
- Response time monitoring
- Memory usage tracking

---

## 📞 SUPPORT & MAINTENANCE

### **Regular Tasks:**
- Database backups
- Log file rotation
- Security updates
- Dependency updates
- Performance optimization

### **Contact Information:**
- **Development Team:** [Contact Details]
- **System Administrator:** [Contact Details]
- **Technical Support:** [Contact Details]

---

## 📝 CHANGE LOG

### **Version 1.0.0 (Current)**
- Initial system release
- Core authentication system
- Document management
- Meeting scheduling
- Messaging system
- SMS notifications
- Research progress tracking

---

## 🎓 ACKNOWLEDGMENTS

**Developed for:** Moshi Co-operative University (MoCU)
**Department:** [Department Name]
**Project:** Student-Supervisor Research Interaction System (SSRIS)

---

*Document Version: 1.0*
*Last Updated: July 2026*
*Maintained by: SSRIS Development Team*
