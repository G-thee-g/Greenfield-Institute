-- ============================================================
-- Greenfield Institute Course Registration System
-- Database Schema — MySQL 8.0+
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS greenfield_db;
CREATE DATABASE greenfield_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE greenfield_db;

-- ============================================================
-- TABLE: users
-- Central authentication table for both students and admins.
-- ============================================================
CREATE TABLE users (
    user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(150)  NOT NULL,
    email         VARCHAR(180)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('student','admin') NOT NULL DEFAULT 'student',
    is_active     TINYINT(1)    NOT NULL DEFAULT 1,
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role  (role)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: students
-- ============================================================
CREATE TABLE students (
    student_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL UNIQUE,
    student_number VARCHAR(20)  NOT NULL UNIQUE,
    department     VARCHAR(100) NOT NULL,
    program        VARCHAR(150) NOT NULL,
    year_level     TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_student_number (student_number),
    INDEX idx_student_user   (user_id)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: admins
-- ============================================================
CREATE TABLE admins (
    admin_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL UNIQUE,
    staff_number VARCHAR(20)  NOT NULL UNIQUE,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: courses
-- ============================================================
CREATE TABLE courses (
    course_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_code     VARCHAR(20)  NOT NULL UNIQUE,
    course_title    VARCHAR(200) NOT NULL,
    description     TEXT,
    department      VARCHAR(100) NOT NULL,
    instructor      VARCHAR(150) NOT NULL,
    credit_hours    TINYINT UNSIGNED NOT NULL DEFAULT 3,
    schedule_day    VARCHAR(60)  NOT NULL,
    schedule_time   VARCHAR(60)  NOT NULL,
    capacity        SMALLINT UNSIGNED NOT NULL DEFAULT 30,
    available_seats SMALLINT UNSIGNED NOT NULL DEFAULT 30,
    status          ENUM('active','inactive','full') NOT NULL DEFAULT 'active',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_capacity  CHECK (capacity > 0),
    CONSTRAINT chk_seats     CHECK (available_seats >= 0),
    CONSTRAINT chk_seats_cap CHECK (available_seats <= capacity),
    INDEX idx_course_code (course_code),
    INDEX idx_department  (department),
    INDEX idx_status      (status)
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: registrations
-- ============================================================
CREATE TABLE registrations (
    registration_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id       INT UNSIGNED NOT NULL,
    course_id        INT UNSIGNED NOT NULL,
    registration_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status           ENUM('registered','waitlisted','dropped') NOT NULL DEFAULT 'registered',
    dropped_at       DATETIME DEFAULT NULL,
    CONSTRAINT uq_student_course UNIQUE (student_id, course_id),
    CONSTRAINT fk_reg_student FOREIGN KEY (student_id)
        REFERENCES students(student_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reg_course FOREIGN KEY (course_id)
        REFERENCES courses(course_id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_reg_student (student_id),
    INDEX idx_reg_course  (course_id),
    INDEX idx_reg_status  (status),
    INDEX idx_reg_date    (registration_date)
) ENGINE=InnoDB;

-- ============================================================
-- TRIGGERS: Maintain available_seats automatically
-- ============================================================
DELIMITER $$

CREATE TRIGGER trg_after_registration_insert
AFTER INSERT ON registrations
FOR EACH ROW
BEGIN
    IF NEW.status = 'registered' THEN
        UPDATE courses
        SET
            available_seats = GREATEST(0, available_seats - 1),
            status = IF((available_seats - 1) <= 0, 'full', 'active')
        WHERE course_id = NEW.course_id;
    END IF;
END$$

CREATE TRIGGER trg_after_registration_update
AFTER UPDATE ON registrations
FOR EACH ROW
BEGIN
    IF OLD.status = 'registered' AND NEW.status = 'dropped' THEN
        UPDATE courses
        SET
            available_seats = LEAST(capacity, available_seats + 1),
            status = 'active'
        WHERE course_id = NEW.course_id;
    END IF;

    IF OLD.status != 'registered' AND NEW.status = 'registered' THEN
        UPDATE courses
        SET
            available_seats = GREATEST(0, available_seats - 1),
            status = IF((available_seats - 1) <= 0, 'full', 'active')
        WHERE course_id = NEW.course_id;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- VIEWS
-- ============================================================
CREATE VIEW v_registration_details AS
SELECT
    r.registration_id,
    r.registration_date,
    r.status          AS registration_status,
    r.dropped_at,
    s.student_id,
    s.student_number,
    u.full_name       AS student_name,
    u.email           AS student_email,
    s.department      AS student_department,
    s.program,
    c.course_id,
    c.course_code,
    c.course_title,
    c.instructor,
    c.credit_hours,
    c.schedule_day,
    c.schedule_time,
    c.department      AS course_department,
    c.capacity,
    c.available_seats
FROM registrations r
JOIN students s ON r.student_id = s.student_id
JOIN users    u ON s.user_id    = u.user_id
JOIN courses  c ON r.course_id  = c.course_id;

CREATE VIEW v_course_summary AS
SELECT
    c.course_id,
    c.course_code,
    c.course_title,
    c.department,
    c.instructor,
    c.credit_hours,
    c.capacity,
    c.available_seats,
    c.status,
    COUNT(CASE WHEN r.status='registered'  THEN 1 END) AS enrolled_count,
    COUNT(CASE WHEN r.status='waitlisted'  THEN 1 END) AS waitlist_count
FROM courses c
LEFT JOIN registrations r ON c.course_id = r.course_id
GROUP BY c.course_id;

-- ============================================================
-- SEED DATA
-- All passwords = "Admin@1234" (bcrypt, cost 12)
-- Use: password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost'=>12])
-- The hash below is the bcrypt hash of the string "password"
-- for demo only — replace with real hashes in production.
-- ============================================================
INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Administrator',  'admin@greenfield.edu',                '$2y$12$LCY0MefVIEc3.XiVCp7HVuH/2HA.imtOknv0EZo0Gx3/0EQvS0oqS', 'admin'),
('John Smith',            'john.smith@students.greenfield.edu',  '$2y$12$LCY0MefVIEc3.XiVCp7HVuH/2HA.imtOknv0EZo0Gx3/0EQvS0oqS', 'student'),
('Alice Chen',            'alice.chen@students.greenfield.edu',  '$2y$12$LCY0MefVIEc3.XiVCp7HVuH/2HA.imtOknv0EZo0Gx3/0EQvS0oqS', 'student'),
('Marcus Johnson',        'marcus.j@students.greenfield.edu',    '$2y$12$LCY0MefVIEc3.XiVCp7HVuH/2HA.imtOknv0EZo0Gx3/0EQvS0oqS', 'student');

INSERT INTO admins (user_id, staff_number) VALUES (1, 'GFI-ADM-001');

INSERT INTO students (user_id, student_number, department, program, year_level) VALUES
(2, 'GFI-9021', 'Computer Science',       'B.S. Computer Science',        3),
(3, 'GFI-9022', 'Computer Science',       'B.S. Computer Science',        2),
(4, 'GFI-9023', 'Business Administration','B.S. Business Administration', 1);

INSERT INTO courses
(course_code,course_title,description,department,instructor,credit_hours,schedule_day,schedule_time,capacity,available_seats,status)
VALUES
('CS101','Introduction to Computer Science','A foundational course covering the basics of programming, algorithms, and computational thinking using Python.','Computer Science','Dr. Alan Turing',3,'Mon, Wed, Fri','9:00 AM - 10:00 AM',40,5,'active'),
('CS201','Data Structures','In-depth study of fundamental data structures and algorithms essential for software engineering.','Computer Science','Dr. A. Turing',4,'Mon, Wed','10:00 AM - 11:30 AM',35,0,'full'),
('CS301','Database Systems','Design and implementation of relational database systems. Covers SQL, normalization, and query optimization.','Computer Science','Dr. Grace Hopper',3,'Tue, Thu','9:00 AM - 10:30 AM',30,12,'active'),
('CS401','Advanced Algorithms','Rigorous exploration of complex algorithmic design and analysis for high-performance computing environments.','Computer Science','Prof. Alan Turing',4,'Mon, Wed, Fri','10:00 AM - 11:30 AM',30,8,'active'),
('BUS310','Corporate Strategy','Analysis of competitive advantage, market positioning, and long-term strategic planning for multinational enterprises.','Business Administration','Prof. M. Porter',3,'Tue, Thu','1:00 PM - 2:30 PM',50,0,'full'),
('BUS210','Financial Accounting','Fundamentals of financial accounting including the preparation and analysis of financial statements.','Business Administration','Prof. J. Keynes',3,'Mon, Wed','2:00 PM - 3:30 PM',45,20,'active'),
('ENG220','Creative Writing','Workshop-based course focusing on fiction and poetry, emphasizing peer review and revision techniques.','Liberal Arts','V. Woolf',3,'Tuesday','2:00 PM - 4:00 PM',25,13,'active'),
('ENG305','Modern Literature','Survey of modernist literary movements from the late 19th century onward with critical analysis.','Liberal Arts','Dr. Virginia Woolf',3,'Tue, Thu','1:00 PM - 2:45 PM',30,10,'active'),
('ART105','Modern Art History','Survey of visual arts from mid-19th century to contemporary movements, focusing on structuralism and modernism.','Liberal Arts','Dr. E. Hopper',3,'Mon, Wed','3:00 PM - 4:30 PM',40,12,'active'),
('MAT210','Linear Algebra','Study of linear maps, vector spaces, and matrix theory with computational and theoretical applications.','Mathematics','Prof. David Hilbert',3,'Wed, Fri','8:00 AM - 9:30 AM',35,13,'active'),
('MAT301','Calculus III','Multivariable calculus covering partial derivatives, multiple integrals, and vector calculus.','Mathematics','Prof. Emmy Noether',4,'Mon, Wed, Fri','11:00 AM - 12:00 PM',40,15,'active'),
('MATH202','Advanced Linear Algebra','Extension of linear algebra including abstract vector spaces and advanced matrix decompositions.','Mathematics','Prof. Ada Lovelace',3,'Mon, Wed, Fri','11:00 AM - 12:00 PM',30,0,'full'),
('PHY102','General Physics I','Mechanics, thermodynamics, and wave phenomena. Laboratory component included.','Engineering','Dr. R. Feynman',4,'Mon, Wed, Fri','1:00 PM - 2:00 PM',35,17,'active'),
('PHY410','Quantum Mechanics','Advanced course in quantum theory including wave mechanics, operators, and perturbation theory.','Engineering','Dr. Richard Feynman',4,'Tue, Thu','10:00 AM - 11:50 AM',20,2,'active');

-- Seed registrations for John Smith (student_id=1)
INSERT INTO registrations (student_id, course_id, status) VALUES
(1, 4, 'registered'),
(1, 8, 'registered'),
(1, 10, 'registered');

SET FOREIGN_KEY_CHECKS = 1;
