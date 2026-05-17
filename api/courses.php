<?php
require_once '../includes/auth.php'; require_once '../includes/functions.php'; header('Content-Type: application/json'); requireLogin();
echo json_encode(['success'=>true,'courses'=>getCourses($_GET)]);
?>
