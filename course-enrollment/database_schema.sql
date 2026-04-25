-- Online Course Enrollment and Progress Tracking System Database Schema

CREATE DATABASE IF NOT EXISTS course_enrollment_db;
USE course_enrollment_db;

-- ============================================
-- TABLE 1: Users
-- ============================================
CREATE TABLE IF NOT EXISTS Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('Student', 'Instructor', 'Admin') NOT NULL DEFAULT 'Student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_username ON Users(username);
CREATE INDEX idx_role ON Users(role);

-- ============================================
-- TABLE 2: Courses
-- ============================================
CREATE TABLE IF NOT EXISTS Courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(150) NOT NULL,
    description TEXT,
    instructor_id INT NOT NULL,
    duration_weeks INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES Users(id) ON DELETE RESTRICT
);

CREATE INDEX idx_instructor ON Courses(instructor_id);

-- ============================================
-- TABLE 3: Modules
-- ============================================
CREATE TABLE IF NOT EXISTS Modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    module_name VARCHAR(150) NOT NULL,
    module_number INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES Courses(id) ON DELETE CASCADE
);

CREATE INDEX idx_course_modules ON Modules(course_id);

-- ============================================
-- TABLE 4: Enrollments
-- ============================================
CREATE TABLE IF NOT EXISTS Enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    date_enrolled DATE NOT NULL,
    status ENUM('Active', 'Completed', 'Dropped') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES Courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id)
);

CREATE INDEX idx_user_id ON Enrollments(user_id);
CREATE INDEX idx_course_id ON Enrollments(course_id);
CREATE INDEX idx_status ON Enrollments(status);

-- ============================================
-- TABLE 5: Progress
-- ============================================
CREATE TABLE IF NOT EXISTS Progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT NOT NULL,
    module_id INT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    score DECIMAL(5, 2),
    completion_date DATE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES Enrollments(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES Modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progress (enrollment_id, module_id)
);

CREATE INDEX idx_enrollment_progress ON Progress(enrollment_id);
CREATE INDEX idx_module_progress ON Progress(module_id);
CREATE INDEX idx_progress_status ON Progress(status);

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Insert Admin User
INSERT INTO Users (username, password, email, full_name, role) 
VALUES ('admin', 'admin123', 'admin@course.com', 'Admin User', 'Admin');

-- Insert Instructor Users
INSERT INTO Users (username, password, email, full_name, role) 
VALUES 
('instructor1', 'instr123', 'instructor1@course.com', 'Dr. John Smith', 'Instructor'),
('instructor2', 'instr123', 'instructor2@course.com', 'Prof. Sarah Johnson', 'Instructor');

-- Insert Student Users
INSERT INTO Users (username, password, email, full_name, role) 
VALUES 
('student1', 'pass123', 'student1@course.com', 'Alice Brown', 'Student'),
('student2', 'pass123', 'student2@course.com', 'Bob Wilson', 'Student'),
('student3', 'pass123', 'student3@course.com', 'Carol White', 'Student'),
('student4', 'pass123', 'student4@course.com', 'David Lee', 'Student'),
('student5', 'pass123', 'student5@course.com', 'Eva Martinez', 'Student');

-- Insert Courses
INSERT INTO Courses (course_name, description, instructor_id, duration_weeks) 
VALUES 
(
    'Web Development Fundamentals',
    'Learn the basics of HTML, CSS, and JavaScript for building responsive web applications.',
    2,
    8
),
(
    'Advanced Python Programming',
    'Master advanced Python concepts including decorators, generators, and asynchronous programming.',
    3,
    10
),
(
    'Database Design & SQL',
    'Learn relational database design principles and SQL optimization techniques.',
    2,
    6
),
(
    'Mobile App Development',
    'Build cross-platform mobile applications using modern frameworks.',
    3,
    12
),
(
    'Cloud Computing with AWS',
    'Deploy and manage applications on Amazon Web Services. Learn EC2, S3, Lambda, and more.',
    2,
    8
);

-- Insert Modules
INSERT INTO Modules (course_id, module_name, module_number, description) 
VALUES 
(1, 'HTML Basics', 1, 'Introduction to HTML5 and semantic markup'),
(1, 'CSS Styling', 2, 'Styling web pages with CSS3 and responsive design'),
(1, 'JavaScript Fundamentals', 3, 'Core JavaScript concepts and DOM manipulation'),
(2, 'Object-Oriented Programming', 1, 'Classes, inheritance, and polymorphism'),
(2, 'Functional Programming', 2, 'Lambda, map, filter, and decorators'),
(2, 'Async Programming', 3, 'Asynchronous operations and coroutines'),
(3, 'Database Normalization', 1, 'Understanding 1NF, 2NF, 3NF'),
(3, 'SQL Queries', 2, 'SELECT, INSERT, UPDATE, DELETE operations'),
(3, 'Query Optimization', 3, 'Indexing and performance tuning'),
(4, 'Mobile Basics', 1, 'Introduction to mobile development'),
(5, 'AWS Fundamentals', 1, 'Introduction to AWS services');

-- Insert Sample Enrollments
INSERT INTO Enrollments (user_id, course_id, date_enrolled, status) 
VALUES 
(4, 1, '2024-01-15', 'Active'),
(4, 2, '2024-02-01', 'Active'),
(5, 1, '2024-01-20', 'Completed'),
(5, 3, '2024-02-05', 'Active'),
(6, 2, '2024-01-25', 'Active'),
(6, 5, '2024-03-01', 'Active'),
(7, 3, '2024-02-10', 'Active'),
(8, 1, '2024-03-05', 'Active');

-- Insert Sample Progress
INSERT INTO Progress (enrollment_id, module_id, status, score, completion_date) 
VALUES 
(1, 1, 'Completed', 85.5, '2024-01-20'),
(1, 2, 'In Progress', NULL, NULL),
(1, 3, 'Pending', NULL, NULL),
(2, 4, 'Completed', 92.0, '2024-02-15'),
(2, 5, 'In Progress', NULL, NULL),
(3, 1, 'Completed', 78.5, '2024-02-01'),
(3, 2, 'Completed', 88.0, '2024-02-10'),
(3, 3, 'Completed', 95.5, '2024-02-20'),
(4, 7, 'Completed', 82.0, '2024-02-20'),
(4, 8, 'In Progress', NULL, NULL),
(5, 4, 'Completed', 90.5, '2024-02-18'),
(5, 5, 'Completed', 87.0, '2024-02-28'),
(6, 11, 'Pending', NULL, NULL),
(7, 1, 'Pending', NULL, NULL),
(8, 1, 'In Progress', NULL, NULL);

-- ============================================
-- VIEWS FOR REPORTING
-- ============================================

CREATE OR REPLACE VIEW StudentEnrollmentReport AS
SELECT 
    u.id,
    u.full_name,
    u.email,
    COUNT(e.id) AS total_enrollments,
    SUM(CASE WHEN e.status = 'Active' THEN 1 ELSE 0 END) AS active_courses,
    SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) AS completed_courses
FROM Users u
LEFT JOIN Enrollments e ON u.id = e.user_id
WHERE u.role = 'Student'
GROUP BY u.id, u.full_name, u.email;

CREATE OR REPLACE VIEW StudentAverageScores AS
SELECT 
    u.full_name,
    u.email,
    COUNT(p.id) AS total_assessments,
    AVG(p.score) AS average_score,
    MAX(p.score) AS highest_score,
    MIN(p.score) AS lowest_score
FROM Users u
JOIN Enrollments e ON u.id = e.user_id
JOIN Progress p ON e.id = p.enrollment_id
WHERE p.score IS NOT NULL
GROUP BY u.id, u.full_name, u.email
ORDER BY average_score DESC;

CREATE OR REPLACE VIEW CourseEnrollmentStats AS
SELECT 
    c.id,
    c.course_name,
    u.full_name AS instructor,
    COUNT(e.id) AS total_enrollments,
    SUM(CASE WHEN e.status = 'Active' THEN 1 ELSE 0 END) AS active_students,
    SUM(CASE WHEN e.status = 'Completed' THEN 1 ELSE 0 END) AS completed_students
FROM Courses c
LEFT JOIN Users u ON c.instructor_id = u.id
LEFT JOIN Enrollments e ON c.id = e.course_id
GROUP BY c.id, c.course_name, u.full_name;

CREATE OR REPLACE VIEW ModuleCompletionStatus AS
SELECT 
    c.course_name,
    m.module_name,
    COUNT(p.id) AS total_attempts,
    SUM(CASE WHEN p.status = 'Completed' THEN 1 ELSE 0 END) AS completed,
    ROUND(AVG(p.score), 2) AS avg_score
FROM Courses c
JOIN Modules m ON c.id = m.course_id
LEFT JOIN Progress p ON m.id = p.module_id
GROUP BY c.id, m.id, c.course_name, m.module_name;

CREATE OR REPLACE VIEW StudentProgressDetails AS
SELECT 
    u.full_name AS student_name,
    c.course_name,
    m.module_name,
    p.status,
    p.score,
    p.completion_date
FROM Users u
JOIN Enrollments e ON u.id = e.user_id
JOIN Courses c ON e.course_id = c.id
JOIN Modules m ON c.id = m.course_id
LEFT JOIN Progress p ON e.id = p.enrollment_id AND m.id = p.module_id
WHERE u.role = 'Student'
ORDER BY u.full_name, c.course_name, m.module_number;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS EnrollStudentInCourse(
    IN p_user_id INT,
    IN p_course_id INT,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_count INT;
    DECLARE v_enrollment_id INT;
    
    SELECT COUNT(*) INTO v_count FROM Users WHERE id = p_user_id AND role = 'Student';
    IF v_count = 0 THEN
        SET p_message = 'User not found or is not a student';
    ELSE
        SELECT COUNT(*) INTO v_count FROM Courses WHERE id = p_course_id;
        IF v_count = 0 THEN
            SET p_message = 'Course not found';
        ELSE
            SELECT COUNT(*) INTO v_count FROM Enrollments 
            WHERE user_id = p_user_id AND course_id = p_course_id;
            IF v_count > 0 THEN
                SET p_message = 'Student already enrolled';
            ELSE
                INSERT INTO Enrollments (user_id, course_id, date_enrolled, status)
                VALUES (p_user_id, p_course_id, CURDATE(), 'Active');
                SET v_enrollment_id = LAST_INSERT_ID();
                INSERT INTO Progress (enrollment_id, module_id, status)
                SELECT v_enrollment_id, id, 'Pending'
                FROM Modules WHERE course_id = p_course_id;
                SET p_message = 'Successfully enrolled';
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;
