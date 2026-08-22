# Student Supervisor Research Interaction System (SSRIS)

A comprehensive web-based system for managing student research projects, proposals, and supervisor relationships in academic institutions.

## System Overview

SSRIS is designed to streamline the research supervision process by providing a centralized platform for:
- Student research project management
- Proposal submission and review
- Supervisor-student assignments
- Meeting scheduling and feedback tracking
- Progress monitoring and reporting

## System Architecture

### Backend Framework
- **Laravel 12.0** - PHP web application framework
- **PHP 8.2+** - Server-side programming language
- **MySQL** - Primary database (configurable for SQLite/PostgreSQL)

### Frontend Technologies
- **Vite 7.0** - Build tool and development server
- **TailwindCSS 4.0** - Utility-first CSS framework
- **Axios** - HTTP client for API requests

### Database Structure

#### Core Tables
1. **ssris_users** - System users with role-based access
   - Roles: admin, supervisor, student
   - Fields: name, username, email, program, reg_number, year, supervisor_id

2. **research_projects** - Research project tracking
   - Status workflow: proposal_submitted → under_review → revision → approved → in_progress → completed
   - Fields: title, description, student_id, supervisor_id, dates, research_area

3. **proposals** - Research proposal management
   - Version control for proposal submissions
   - File upload support
   - Review tracking with comments

4. **meetings** - Supervisor-student meeting management
   - Scheduling and attendance tracking
   - Meeting notes and action items

5. **feedback** - Feedback and evaluation system
   - Structured feedback collection
   - Performance metrics

## System Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **Web Server**: Apache, Nginx, or PHP built-in server
- **Database**: MySQL 5.7+ (primary), SQLite, or PostgreSQL 9.6+
- **Extensions**: 
  - OpenSSL PHP Extension
  - PDO PHP Extension
  - Mbstring PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension
  - Ctype PHP Extension
  - Fileinfo PHP Extension

### Development Requirements
- **Node.js**: 18.0 or higher
- **npm**: 9.0 or higher
- **Composer**: 2.0 or higher

## Installation & Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd ssris
```

### 2. Install Dependencies
```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Build Assets
```bash
npm run build
```

### 6. Start Development Server
```bash
# Start Laravel server
php artisan serve

# Start Vite development server (in separate terminal)
npm run dev
```

## Quick Setup Script
Use the automated setup command:
```bash
composer run setup
```

This command will:
- Install all dependencies
- Configure environment
- Run database migrations
- Build frontend assets

## Development Workflow

### Running Development Environment
```bash
composer run dev
```
This starts:
- Laravel development server
- Queue worker
- Log monitoring
- Vite development server

### Testing
```bash
composer run test
```

### Code Formatting
```bash
./vendor/bin/pint
```

## User Roles & Permissions

### Administrator
- User management (create, edit, delete users)
- System configuration
- Report generation
- Global oversight

### Supervisor
- View assigned students
- Review research proposals
- Schedule meetings
- Provide feedback
- Monitor progress

### Student
- Submit research proposals
- View supervisor assignments
- Track project progress
- Schedule meetings
- Access feedback

## Project Structure

```
ssris/
├── app/
│   ├── Http/          # Controllers, middleware, requests
│   ├── Models/        # Eloquent models
│   └── Providers/     # Service providers
├── database/
│   ├── migrations/    # Database schema
│   ├── seeders/       # Sample data
│   └── factories/     # Model factories
├── resources/
│   ├── views/         # Blade templates
│   └── js/           # Frontend assets
├── routes/            # API and web routes
├── storage/           # File storage
└── public/           # Web accessible files
```

## Configuration

### Environment Variables
Key configuration options in `.env`:
- `APP_NAME` - Application name
- `DB_CONNECTION` - Database driver
- `MAIL_MAILER` - Email configuration
- `QUEUE_CONNECTION` - Queue driver

### Database Configuration
Default uses MySQL. For SQLite/PostgreSQL:
```env
# For SQLite
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

# For PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ssris
DB_USERNAME=postgres
DB_PASSWORD=password
```

## Security Features

- Password hashing with bcrypt
- CSRF protection
- Input validation and sanitization
- Role-based access control
- Secure file uploads
- SQL injection prevention via Eloquent ORM

## Contributing

1. Fork the repository
2. Create feature branch
3. Make changes with proper testing
4. Submit pull request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For technical support or questions:
- Review the documentation
- Check existing issues
- Contact the development team
