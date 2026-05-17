<<<<<<< HEAD
# Greenfield Course Registration PHP Project

This project is rebuilt to match your existing `greenfield_registration.sql` schema. It uses your tables and columns:

- `users.user_id`, `users.full_name`, `users.password_hash`
- `students.student_id`, `students.student_number`
- `courses.course_id`, `courses.course_code`, `courses.available_seats`
- `registrations.registration_id`
- your triggers for automatically updating `available_seats`
- your views `v_registration_details` and `v_course_summary`

## Install in XAMPP

1. Extract this folder into:

```text
C:\xampp\htdocs\greenfield-course-registration
```

2. Open phpMyAdmin and import:

```text
database/greenfield_registration.sql
```

3. Check database settings in:

```text
includes/db.php
```

Default settings:

```php
$DB_HOST = 'localhost';
$DB_NAME = 'greenfield_db';
$DB_USER = 'root';
$DB_PASS = '';
```

4. Open:

```text
http://localhost/greenfield-course-registration/
```

## Demo login

Use the seed users from your SQL:

```text
Admin:   admin@greenfield.edu / password
Student: GFI-9021 / password
```

The first successful demo login will rewrite the broken seed hash to a valid PHP bcrypt hash. This does not change your schema.

## What was fixed

- Login username is no longer hard-coded.
- Student/admin dashboard names use the logged-in user.
- Admin-added courses save to the `courses` table.
- Student course catalog reads from the same `courses` table.
- Student registration/drop updates the `registrations` table.
- Your MySQL triggers update `available_seats` automatically.
- Registration validates password length, password match, email, full name, and student ID format.
=======
# Greenfield-Institute
A PHP/MySQL course registration system for Greenfield Institute with student enrollment, admin course management, session-based authentication, and real-time seat availability tracking.
>>>>>>> e7ee193b3a34885a8d3f3618b088813a13cd9e43
