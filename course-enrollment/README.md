# Online Course Enrollment and Progress Tracking System

A comprehensive web-based platform for managing course enrollments, tracking student progress, and generating performance reports for online learning.

## Project Overview

This system provides a complete backend database solution with an interactive frontend interface for:
- **Student Management**: Course enrollment, progress tracking, and performance monitoring
- **Course Management**: Create, edit, and manage courses by administrators
- **Instructor Tools**: Monitor student progress and course statistics
- **Admin Dashboard**: System-wide analytics and enrollment management

## Features

### Student Features
- Browse available courses
- Enroll in courses with one-click enrollment
- Track personal progress across modules
- View scores and completion status
- Generate personalized performance reports
- Real-time dashboard with enrollment statistics

### Admin Features
- Complete course management (add, edit, delete)
- Enrollment management and status updates
- System-wide analytics and statistics
- View recent enrollments
- Course performance metrics

### Instructor Features
- View assigned courses
- Monitor student progress in their courses
- View student scores and module completion
- Course-wise enrollment statistics

## Technology Stack

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL 5.7+** - Relational database
- **Session Management** - For user authentication

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with gradients and animations
- **Vanilla JavaScript** - Interactive functionality
- **Bootstrap-inspired** - Responsive design

### Database Features
- **Views** - Pre-built queries for reporting
- **Stored Procedures** - Business logic encapsulation
- **Normalization** - 3NF database design
- **Referential Integrity** - Foreign key constraints

## Installation & Setup

### Prerequisites
- XAMPP/WAMP/LAMP with PHP 7.4+
- MySQL 5.7 or MariaDB
- Modern web browser

### Step 1: Extract Files
```
Extract the project zip file to:
htdocs/ (XAMPP) or www/ (WAMP)
```

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `course_enrollment_db`
3. Import the `database_schema.sql` file:
   - Select the database
   - Go to Import tab
   - Choose `database_schema.sql`
   - Click Import

### Step 3: Verify Configuration
The `config.php` file contains:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'course_enrollment_db');
```

Update these values if your MySQL credentials differ.

### Step 4: Access the Application
```
http://localhost/course-enrollment/login.html
```

## Demo Credentials

### Student Login
- **Username**: student1
- **Password**: pass123

### Admin Login
- **Username**: admin
- **Password**: admin123

### Instructor Login
- **Username**: instructor1
- **Password**: instr123

## File Structure

```
project/
├── login.html                      # Main login page
├── student_dashboard.html          # Student home page
├── courses.html                    # Course browsing
├── progress.html                   # Progress tracking
├── reports.html                    # Student reports
├── admin_dashboard.html            # Admin home
├── manage_courses.html             # Course management
├── manage_enrollments.html         # Enrollment management
├── instructor_dashboard.html       # Instructor home
├── database_schema.sql             # Database setup
├── config.php                      # Database configuration
├── backend/
│   ├── login.php                   # Login processing
│   ├── logout.php                  # Logout handler
│   ├── check_session.php           # Session validation
│   ├── get_student_dashboard.php   # Student data
│   ├── get_courses.php             # Course listing
│   ├── enroll_course.php           # Enrollment processing
│   ├── get_progress.php            # Progress data
│   ├── get_reports.php             # Reports data
│   ├── get_admin_dashboard.php     # Admin statistics
│   ├── manage_course.php           # Course CRUD
│   ├── get_all_courses.php         # All courses listing
│   ├── get_all_enrollments.php     # All enrollments
│   ├── manage_enrollment.php       # Enrollment management
│   └── get_instructor_dashboard.php # Instructor data
└── README.md                       # This file
```

## Database Schema

### Tables
- **Users** - User accounts and roles
- **Courses** - Course information
- **Modules** - Course modules/sections
- **Enrollments** - Student course enrollments
- **Progress** - Module completion tracking

### Views
- `StudentEnrollmentReport` - Student enrollment summary
- `StudentAverageScores` - Performance analytics
- `CourseEnrollmentStats` - Course statistics
- `ModuleCompletionStatus` - Module metrics
- `StudentProgressDetails` - Detailed progress view

### Stored Procedures
- `EnrollStudentInCourse()` - Automated enrollment
- `UpdateStudentProgress()` - Progress updates
- `GetStudentDashboardStats()` - Student statistics
- `GetAdminDashboardStats()` - System statistics

## User Workflows

### Student Workflow
1. Login with credentials
2. View dashboard with enrollment summary
3. Browse available courses
4. Enroll in desired courses
5. Track progress across modules
6. View performance reports

### Admin Workflow
1. Login as admin
2. View system statistics
3. Manage courses (add/edit/delete)
4. Manage enrollments
5. Monitor system-wide metrics

### Instructor Workflow
1. Login as instructor
2. View assigned courses
3. Monitor student enrollment
4. Track student progress
5. View performance metrics

## Database Queries

The database includes sample queries for:
```sql
-- Identify user roles
SELECT DISTINCT role FROM Users;

-- Get enrollments with details
SELECT u.full_name, c.course_name, e.date_enrolled, e.status
FROM Enrollments e
JOIN Users u ON e.user_id = u.id
JOIN Courses c ON e.course_id = c.id
ORDER BY e.date_enrolled DESC;

-- Get top performers
SELECT u.full_name, AVG(p.score) AS avg_score
FROM Users u
JOIN Enrollments e ON u.id = e.user_id
JOIN Progress p ON e.id = p.enrollment_id
WHERE p.score IS NOT NULL
GROUP BY u.id, u.full_name
ORDER BY avg_score DESC;

-- Course completion rates
SELECT c.course_name, 
       COUNT(e.id) AS total_enrollments,
       SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) AS completed,
       ROUND(SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) / COUNT(e.id) * 100, 2) AS completion_rate
FROM Courses c
LEFT JOIN Enrollments e ON c.id = e.course_id
GROUP BY c.id, c.course_name;
```

## Design Features

### Frontend
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Gradient Backgrounds** - Modern purple gradient theme
- **Smooth Animations** - CSS animations for better UX
- **Card-based Layout** - Clean, organized interface
- **Loading States** - Spinner animations while fetching data
- **Form Validation** - Client-side input validation
- **Modal Dialogs** - For add/edit operations

### Backend
- **Session Management** - Secure user sessions
- **Role-based Access** - Different access for Student/Admin/Instructor
- **Parameterized Queries** - SQL injection protection
- **JSON API** - RESTful API endpoints
- **Error Handling** - Comprehensive error messages
- **Data Validation** - Input validation before processing

## Security Considerations

### Implemented
- Session-based authentication
- Role-based access control
- Parameterized SQL queries
- Input validation
- CSRF protection via sessions

### Recommendations for Production
- Use password hashing (bcrypt/argon2)
- Implement HTTPS/SSL
- Add CSRF tokens
- Use prepared statements everywhere
- Implement rate limiting
- Add API keys for external access
- Regular security audits

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check `config.php` settings
- Ensure database `course_enrollment_db` exists

### Login Issues
- Clear browser cache
- Verify user exists in database
- Check role selection matches user role

### Page Not Loading
- Check file permissions
- Verify all backend files exist
- Check PHP error logs

### Data Not Displaying
- Verify database connection
- Check browser console for errors
- Ensure sample data was imported

## Performance Optimization

The system includes:
- Database indexes on frequently queried columns
- Proper query optimization
- Efficient JOIN operations
- View-based reporting

## Future Enhancements

- Real-time notifications
- Email notifications for enrollments
- Discussion forums
- File uploads for assignments
- Grading system
- Certificate generation
- Mobile app
- Advanced analytics
- Video streaming integration

## Support & Documentation

For detailed documentation on each module:
1. Review the SQL schema comments
2. Check stored procedure documentation
3. Examine backend API endpoints
4. Study frontend JavaScript comments

## License

This project is created for educational purposes as part of a TCS Industry Project.

## Project Information

- **Course**: Database Management System
- **Focus**: Relational Database Design & SQL Implementation
- **Concepts**: Schema Design, Normalization, Views, Stored Procedures, Queries
- **Tools**: MySQL, PHP, HTML, CSS, JavaScript

---

**Last Updated**: April 2026
**Version**: 1.0
