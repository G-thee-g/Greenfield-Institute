<?php
require_once '../includes/auth.php'; require_once '../includes/functions.php'; header('Content-Type: application/json'); requireAdmin();
echo json_encode(['success'=>true,'registrations'=>getAllRegistrations($_GET)]);
?>
