<?php
require_once __DIR__ . '/db.php';

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }

function getInitials(?string $name): string {
    $parts = preg_split('/\s+/', trim((string)$name));
    $initials = '';
    foreach ($parts as $part) if ($part !== '') $initials .= strtoupper(substr($part, 0, 1));
    return substr($initials ?: 'U', 0, 2);
}

function getStudentByUserId(int $userId): ?array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM students WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getCourses(array $filters = []): array {
    global $pdo;
    $sql = "SELECT * FROM courses WHERE 1=1";
    $params = [];
    $search = trim($filters['search'] ?? '');
    $dept = trim($filters['dept'] ?? ($filters['department'] ?? ''));
    $status = trim($filters['status'] ?? '');
    $available = $filters['available'] ?? '';

    if ($search !== '') {
        $sql .= " AND (course_code LIKE ? OR course_title LIKE ? OR instructor LIKE ?)";
        $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
    }
    if ($dept !== '') { $sql .= " AND department = ?"; $params[] = $dept; }
    if ($status !== '') { $sql .= " AND status = ?"; $params[] = $status; }
    if ($available === 'available') $sql .= " AND status = 'active' AND available_seats > 0";
    if ($available === 'full') $sql .= " AND (status = 'full' OR available_seats <= 0)";
    $sql .= " ORDER BY course_code ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getCourseById(int $courseId): ?array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE course_id = ? LIMIT 1');
    $stmt->execute([$courseId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function addCourse(array $data): array {
    global $pdo;
    $courseCode = strtoupper(trim($data['course_code'] ?? ''));
    $courseTitle = trim($data['course_title'] ?? '');
    $description = trim($data['description'] ?? '');
    $department = trim($data['department'] ?? '');
    $instructor = trim($data['instructor'] ?? '');
    $creditHours = (int)($data['credit_hours'] ?? 0);
    $scheduleDay = trim($data['schedule_day'] ?? '');
    $scheduleTime = trim($data['schedule_time'] ?? '');
    $capacity = (int)($data['capacity'] ?? 0);
    $status = $data['status'] ?? 'active';

    if ($courseCode === '' || $courseTitle === '' || $department === '' || $instructor === '' || $scheduleDay === '' || $scheduleTime === '') return ['success' => false, 'message' => 'Please fill in all required fields.'];
    if ($creditHours < 1 || $creditHours > 6) return ['success' => false, 'message' => 'Credit hours must be between 1 and 6.'];
    if ($capacity < 1) return ['success' => false, 'message' => 'Capacity must be at least 1.'];
    if (!in_array($status, ['active','inactive','full'], true)) $status = 'active';
    $availableSeats = ($status === 'full') ? 0 : $capacity;

    try {
        $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_title, description, department, instructor, credit_hours, schedule_day, schedule_time, capacity, available_seats, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$courseCode, $courseTitle, $description, $department, $instructor, $creditHours, $scheduleDay, $scheduleTime, $capacity, $availableSeats, $status]);
        return ['success' => true, 'message' => 'Course added successfully.'];
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') return ['success' => false, 'message' => 'Course code already exists.'];
        return ['success' => false, 'message' => 'Failed to add course: ' . $e->getMessage()];
    }
}

function updateCourse(int $courseId, array $data): array {
    global $pdo;
    $course = getCourseById($courseId);
    if (!$course) return ['success' => false, 'message' => 'Course not found.'];

    $courseCode = strtoupper(trim($data['course_code'] ?? $course['course_code']));
    $courseTitle = trim($data['course_title'] ?? $course['course_title']);
    $description = trim($data['description'] ?? $course['description']);
    $department = trim($data['department'] ?? $course['department']);
    $instructor = trim($data['instructor'] ?? $course['instructor']);
    $creditHours = (int)($data['credit_hours'] ?? $course['credit_hours']);
    $scheduleDay = trim($data['schedule_day'] ?? $course['schedule_day']);
    $scheduleTime = trim($data['schedule_time'] ?? $course['schedule_time']);
    $capacity = (int)($data['capacity'] ?? $course['capacity']);
    $status = $data['status'] ?? $course['status'];
    if ($creditHours < 1 || $creditHours > 6 || $capacity < 1) return ['success' => false, 'message' => 'Invalid credit hours or capacity.'];
    if (!in_array($status, ['active','inactive','full'], true)) $status = 'active';
    $enrolled = (int)$course['capacity'] - (int)$course['available_seats'];
    $availableSeats = max(0, $capacity - $enrolled);
    if ($status === 'full') $availableSeats = 0;

    try {
        $stmt = $pdo->prepare("UPDATE courses SET course_code=?, course_title=?, description=?, department=?, instructor=?, credit_hours=?, schedule_day=?, schedule_time=?, capacity=?, available_seats=?, status=? WHERE course_id=?");
        $stmt->execute([$courseCode, $courseTitle, $description, $department, $instructor, $creditHours, $scheduleDay, $scheduleTime, $capacity, $availableSeats, $status, $courseId]);
        return ['success' => true, 'message' => 'Course updated successfully.'];
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') return ['success' => false, 'message' => 'Course code already exists.'];
        return ['success' => false, 'message' => 'Failed to update course.'];
    }
}

function getAllRegistrations(array $filters = []): array {
    global $pdo;
    $sql = "SELECT * FROM v_registration_details WHERE 1=1";
    $params = [];
    $search = trim($filters['search'] ?? '');
    $status = trim($filters['status'] ?? '');
    if ($search !== '') {
        $sql .= " AND (student_number LIKE ? OR student_name LIKE ? OR course_code LIKE ? OR course_title LIKE ?)";
        for ($i=0;$i<4;$i++) $params[] = "%$search%";
    }
    if ($status !== '') { $sql .= " AND registration_status = ?"; $params[] = $status; }
    $sql .= " ORDER BY registration_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getAdminStats(): array {
    global $pdo;
    return [
        'total_courses' => (int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
        'total_students' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
        'registrations' => (int)$pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'registered'")->fetchColumn(),
        'available_courses' => (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active' AND available_seats > 0")->fetchColumn(),
    ];
}

function getStudentCourses(int $userId): array {
    global $pdo;
    $student = getStudentByUserId($userId);
    if (!$student) return [];
    $stmt = $pdo->prepare("SELECT * FROM v_registration_details WHERE student_id = ? AND registration_status = 'registered' ORDER BY registration_date DESC");
    $stmt->execute([$student['student_id']]);
    return $stmt->fetchAll();
}

function getStudentStats(int $userId): array {
    global $pdo;
    $student = getStudentByUserId($userId);
    if (!$student) return ['registered_courses' => 0, 'total_credits' => 0, 'available_courses' => 0];
    $stmt = $pdo->prepare("SELECT COUNT(*) AS registered_courses, COALESCE(SUM(credit_hours),0) AS total_credits FROM v_registration_details WHERE student_id = ? AND registration_status = 'registered'");
    $stmt->execute([$student['student_id']]);
    $registered = $stmt->fetch();
    $available = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active' AND available_seats > 0")->fetchColumn();
    return ['registered_courses' => (int)$registered['registered_courses'], 'total_credits' => (int)$registered['total_credits'], 'available_courses' => $available];
}

function enrollCourse(int $userId, int $courseId): array {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $student = getStudentByUserId($userId);
        if (!$student) throw new Exception('Student profile not found.');
        $stmt = $pdo->prepare('SELECT course_id, available_seats, status FROM courses WHERE course_id = ? FOR UPDATE');
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        if (!$course) throw new Exception('Course not found.');
        if ($course['status'] !== 'active' || (int)$course['available_seats'] <= 0) throw new Exception('This course is full or inactive.');

        $check = $pdo->prepare('SELECT registration_id, status FROM registrations WHERE student_id = ? AND course_id = ? LIMIT 1');
        $check->execute([$student['student_id'], $courseId]);
        $existing = $check->fetch();
        if ($existing && $existing['status'] === 'registered') throw new Exception('You are already registered for this course.');
        if ($existing) {
            $upd = $pdo->prepare("UPDATE registrations SET status='registered', dropped_at=NULL, registration_date=NOW() WHERE registration_id=?");
            $upd->execute([$existing['registration_id']]);
        } else {
            $ins = $pdo->prepare("INSERT INTO registrations (student_id, course_id, status) VALUES (?, ?, 'registered')");
            $ins->execute([$student['student_id'], $courseId]);
        }
        $pdo->commit();
        return ['success' => true, 'message' => 'Course registered successfully.'];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function dropCourse(int $userId, int $courseId): array {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $student = getStudentByUserId($userId);
        if (!$student) throw new Exception('Student profile not found.');
        $stmt = $pdo->prepare("SELECT registration_id FROM registrations WHERE student_id = ? AND course_id = ? AND status = 'registered' FOR UPDATE");
        $stmt->execute([$student['student_id'], $courseId]);
        $reg = $stmt->fetch();
        if (!$reg) throw new Exception('Active registration not found.');
        $drop = $pdo->prepare("UPDATE registrations SET status='dropped', dropped_at=NOW() WHERE registration_id=?");
        $drop->execute([$reg['registration_id']]);
        $pdo->commit();
        return ['success' => true, 'message' => 'Course dropped successfully.'];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>
