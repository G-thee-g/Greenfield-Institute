<?php
require_once '../includes/auth.php'; require_once '../includes/functions.php'; header('Content-Type: application/json');
if (!isLoggedIn() || $_SESSION['role'] !== 'student') { echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }
echo json_encode(dropCourse((int)$_SESSION['user_id'], (int)($_POST['course_id'] ?? 0)));
?>
