-- College Management System - Comprehensive Schema Upgrade
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Modify users table to support all roles, profile, soft delete, reset token
ALTER TABLE users 
    MODIFY COLUMN role ENUM('admin','operator','faculty','student') NOT NULL DEFAULT 'student',
    ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL AFTER role,
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL AFTER avatar,
    ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active' AFTER phone,
    ADD COLUMN IF NOT EXISTS reset_token VARCHAR(100) NULL AFTER status,
    ADD COLUMN IF NOT EXISTS reset_expires_at DATETIME NULL AFTER reset_token,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at;

-- 2. Modify students table for photos, soft delete, guardian, semester, gender
ALTER TABLE students
    ADD COLUMN IF NOT EXISTS photo VARCHAR(255) NULL AFTER name,
    ADD COLUMN IF NOT EXISTS gender ENUM('male','female','other') NULL AFTER photo,
    ADD COLUMN IF NOT EXISTS guardian_name VARCHAR(100) NULL AFTER address,
    ADD COLUMN IF NOT EXISTS guardian_phone VARCHAR(20) NULL AFTER guardian_name,
    ADD COLUMN IF NOT EXISTS semester TINYINT NOT NULL DEFAULT 1 AFTER course_id,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at;

-- 3. Modify faculty table
ALTER TABLE faculty
    ADD COLUMN IF NOT EXISTS photo VARCHAR(255) NULL AFTER name,
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL AFTER email,
    ADD COLUMN IF NOT EXISTS qualification VARCHAR(150) NULL AFTER designation,
    ADD COLUMN IF NOT EXISTS joining_date DATE NULL AFTER qualification,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at;

-- 4. Modify departments table
ALTER TABLE departments
    ADD COLUMN IF NOT EXISTS head_of_department VARCHAR(100) NULL AFTER code,
    ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER head_of_department,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at;

-- 5. Modify courses table
ALTER TABLE courses
    ADD COLUMN IF NOT EXISTS credits TINYINT NOT NULL DEFAULT 3 AFTER duration_years,
    ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER credits,
    ADD COLUMN IF NOT EXISTS fee_per_year DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER description,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at;

-- 6. Modify fees table
ALTER TABLE fees
    ADD COLUMN IF NOT EXISTS fee_type VARCHAR(100) NOT NULL DEFAULT 'Tuition Fee' AFTER student_id,
    ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL AFTER created_at;

-- 7. Roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 8. Permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    module VARCHAR(50) NOT NULL,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 9. Role Permissions mapping
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. System Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 11. Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    target_role VARCHAR(50) NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','success','warning','danger') NOT NULL DEFAULT 'info',
    link VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Fee Payments / Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fee_id INT NOT NULL,
    student_id INT NOT NULL,
    transaction_no VARCHAR(50) NOT NULL UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','card','upi_online','bank_transfer','cheque') NOT NULL DEFAULT 'cash',
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes VARCHAR(255) NULL,
    recorded_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fee_id) REFERENCES fees(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 13. Complaints / Grievances / Helpdesk table
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_no VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    category ENUM('academic','hostel','fees','library','infrastructure','other') NOT NULL DEFAULT 'academic',
    description TEXT NOT NULL,
    attachment VARCHAR(255) NULL,
    status ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
    priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    admin_notes TEXT NULL,
    resolved_at DATETIME NULL,
    deleted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 14. Library Books Inventory table
CREATE TABLE IF NOT EXISTS library_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(30) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    rack_location VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 15. Library Issues / Borrowings table
CREATE TABLE IF NOT EXISTS library_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    fine_amount DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    status ENUM('issued','returned','overdue') NOT NULL DEFAULT 'issued',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES library_books(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 16. Password Resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(100) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- Seed default Roles
INSERT INTO roles (name, label, description) VALUES
('admin', 'System Administrator', 'Full system access and configurations'),
('operator', 'Manager / Academic Operator', 'Manages academic operations, transactions, and reports'),
('faculty', 'Faculty / Staff Member', 'Manages courses, marks attendance, and posts notices'),
('student', 'Student / Customer', 'Access to view attendance, results, fees, complaints, library')
ON DUPLICATE KEY UPDATE label = VALUES(label), description = VALUES(description);

-- Seed default Permissions
INSERT INTO permissions (name, module, description) VALUES
('dashboard.view', 'Dashboard', 'View dashboard metrics and charts'),
('users.manage', 'Users', 'Create, update, delete system users'),
('roles.manage', 'Roles', 'Configure roles and assign permissions'),
('students.view', 'Students', 'View student directories and records'),
('students.manage', 'Students', 'Create, edit, soft-delete, and restore students'),
('departments.manage', 'Departments', 'Create, edit, and delete departments'),
('courses.manage', 'Courses', 'Create, edit, and delete courses'),
('faculty.manage', 'Faculty', 'Manage faculty and staff records'),
('attendance.mark', 'Attendance', 'Record and update attendance'),
('results.manage', 'Results', 'Enter exam marks and publish transcripts'),
('fees.manage', 'Fees & Transactions', 'Create invoices, record payments, and print receipts'),
('complaints.manage', 'Complaints', 'Review and resolve helpdesk complaints'),
('complaints.submit', 'Complaints', 'Submit student or staff complaints'),
('library.manage', 'Library', 'Manage library catalog and book issues'),
('reports.view', 'Reports', 'Generate and export PDF, Excel, and Print reports'),
('settings.manage', 'Settings', 'Update institution settings, logo, and email configs'),
('activity_logs.view', 'Activity Logs', 'Audit trails and system security activity')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Map Admin to all permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.name = 'admin';

-- Map Operator permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN (
    'dashboard.view', 'students.view', 'students.manage', 'departments.manage', 
    'courses.manage', 'faculty.manage', 'attendance.mark', 'results.manage', 
    'fees.manage', 'complaints.manage', 'library.manage', 'reports.view', 'activity_logs.view'
) WHERE r.name = 'operator';

-- Map Faculty permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN (
    'dashboard.view', 'students.view', 'courses.manage', 'attendance.mark', 
    'results.manage', 'complaints.submit', 'library.manage'
) WHERE r.name = 'faculty';

-- Map Student permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN (
    'dashboard.view', 'complaints.submit', 'library.manage'
) WHERE r.name = 'student';

-- Seed default Settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('institute_name', 'Metropolitan Institute of Technology & Management', 'general'),
('institute_email', 'contact@college.local', 'general'),
('institute_phone', '+1 (555) 234-5678', 'general'),
('institute_address', '100 University Boulevard, Tech Campus, NY 10001', 'general'),
('academic_year', '2026-2027', 'general'),
('currency_symbol', '$', 'general'),
('currency_code', 'USD', 'general'),
('email_notifications', '1', 'notifications'),
('theme_mode', 'light', 'appearance'),
('allow_registration', '1', 'auth')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Seed Operator User
INSERT INTO users (name, email, password, role, status, created_at)
VALUES ('Academic Dean (Operator)', 'operator@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'operator', 'active', NOW())
ON DUPLICATE KEY UPDATE name = name;

-- Seed Sample Faculty User
INSERT INTO users (name, email, password, role, status, created_at)
VALUES ('Dr. Alan Turing', 'faculty@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'faculty', 'active', NOW())
ON DUPLICATE KEY UPDATE name = name;

-- Seed Sample Student User
INSERT INTO users (name, email, password, role, status, created_at)
VALUES ('Jane Smith (Student)', 'student@college.local', '$2y$10$oLfYbyaIKXvY0j1l.Z.sQ.OgyPnySUXtKQjdgK/xlhn6qZSAVdUd2', 'student', 'active', NOW())
ON DUPLICATE KEY UPDATE name = name;

-- Seed Sample Books
INSERT INTO library_books (isbn, title, author, category, total_copies, available_copies, rack_location) VALUES
('978-0131103627', 'The C Programming Language', 'Brian W. Kernighan, Dennis M. Ritchie', 'Computer Science', 5, 5, 'CS-RACK-01'),
('978-0262033848', 'Introduction to Algorithms', 'Thomas H. Cormen', 'Computer Science', 8, 7, 'CS-RACK-02'),
('978-0132350884', 'Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', 'Software Engineering', 6, 6, 'SE-RACK-01'),
('978-1119577553', 'Principles of Management', 'Harold Koontz', 'Business', 4, 4, 'BA-RACK-01')
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Seed Sample Notification
INSERT INTO notifications (user_id, target_role, title, message, type, link) VALUES
(NULL, NULL, 'Fall 2026 Semester Registration Open', 'All students and faculty are reminded that registration and syllabus verification for Fall 2026 is now active.', 'info', 'courses'),
(NULL, 'student', 'Tuition Fee Payment Reminder', 'The deadline for Fall semester fee payment is approaching. Please visit the Fees module to pay.', 'warning', 'fees')
ON DUPLICATE KEY UPDATE title = VALUES(title);
