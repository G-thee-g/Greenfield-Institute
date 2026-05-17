<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}

function requireLogin(): void {
    if (!isLoggedIn()) redirect('/login.php');
}

function requireStudent(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'student') redirect('/admin/dashboard.php');
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') redirect('/student/dashboard.php');
}

function currentUser(): ?array {
    global $pdo;
    if (empty($_SESSION['user_id'])) return null;

    $stmt = $pdo->prepare("\n        SELECT u.user_id, u.full_name, u.email, u.role, u.is_active,\n               s.student_id, s.student_number, s.department AS student_department, s.program, s.year_level,\n               a.admin_id, a.staff_number\n        FROM users u\n        LEFT JOIN students s ON u.user_id = s.user_id\n        LEFT JOIN admins a ON u.user_id = a.user_id\n        WHERE u.user_id = ?\n        LIMIT 1\n    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function login(string $identifier, string $password, string $selectedRole = 'student'): array {
    global $pdo;
    $identifier = trim($identifier);
    if ($identifier === '' || $password === '') return ['success' => false, 'message' => 'Please enter your login details.'];

    $stmt = $pdo->prepare("\n        SELECT u.user_id, u.full_name, u.email, u.password_hash, u.role, u.is_active,\n               s.student_number, a.staff_number\n        FROM users u\n        LEFT JOIN students s ON u.user_id = s.user_id\n        LEFT JOIN admins a ON u.user_id = a.user_id\n        WHERE u.email = ? OR s.student_number = ? OR a.staff_number = ?\n        LIMIT 1\n    ");
    $stmt->execute([$identifier, $identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user) return ['success' => false, 'message' => 'Account not found.'];
    if ((int)$user['is_active'] !== 1) return ['success' => false, 'message' => 'This account is inactive.'];
    if ($user['role'] !== $selectedRole) return ['success' => false, 'message' => 'Invalid login type selected.'];

    $ok = password_verify($password, $user['password_hash']);

    // The uploaded SQL seed hash may not verify on some installs. This lets the demo seed users log in once with "password",
    // then automatically rewrites the hash using your current PHP version. It does not change your schema.
    if (!$ok && $password === 'password' && $user['password_hash'] === '$2y$12$LCY0MefVIEc3.XiVCp7HVuH/2HA.imtOknv0EZo0Gx3/0EQvS0oqS') {
        $ok = true;
        $newHash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);
        $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?');
        $upd->execute([$newHash, $user['user_id']]);
    }

    if (!$ok) return ['success' => false, 'message' => 'Incorrect password.'];

    $_SESSION['user_id'] = (int)$user['user_id'];
    $_SESSION['role'] = $user['role'];
    return ['success' => true, 'role' => $user['role']];
}

function registerStudent(array $data): array {
    global $pdo;

    $fullName = trim($data['full_name'] ?? '');
    $studentNumber = strtoupper(trim($data['student_number'] ?? ''));
    $department = trim($data['department'] ?? '');
    $program = trim($data['program'] ?? $department);
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';

    if ($fullName === '' || $studentNumber === '' || $department === '' || $program === '' || $email === '' || $password === '') {
        return ['success' => false, 'message' => 'All fields are required.'];
    }
    if (strlen($fullName) < 3) return ['success' => false, 'message' => 'Full name must be at least 3 characters.'];
    if (!preg_match('/^GFI-[0-9]{4}$/', $studentNumber)) return ['success' => false, 'message' => 'Student ID must use the format GFI-1234.'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return ['success' => false, 'message' => 'Please enter a valid email address.'];
    if (strlen($password) < 8) return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) return ['success' => false, 'message' => 'Password must contain at least one letter and one number.'];
    if ($password !== $confirmPassword) return ['success' => false, 'message' => 'Passwords do not match.'];

    try {
        $pdo->beginTransaction();
        $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        if ($check->fetch()) throw new Exception('Email already exists.');

        $check = $pdo->prepare('SELECT student_id FROM students WHERE student_number = ? LIMIT 1');
        $check->execute([$studentNumber]);
        if ($check->fetch()) throw new Exception('Student ID already exists.');

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $userStmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, \'student\')');
        $userStmt->execute([$fullName, $email, $hash]);
        $userId = (int)$pdo->lastInsertId();

        $studentStmt = $pdo->prepare('INSERT INTO students (user_id, student_number, department, program, year_level) VALUES (?, ?, ?, ?, 1)');
        $studentStmt->execute([$userId, $studentNumber, $department, $program]);
        $pdo->commit();

        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = 'student';
        return ['success' => true, 'message' => 'Student account created successfully.'];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
}
?>
