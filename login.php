<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) redirect($_SESSION['role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = login(trim($_POST['identifier'] ?? ''), $_POST['password'] ?? '', $_POST['role'] ?? 'student');
    if ($result['success']) redirect($result['role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php');
    $error = $result['message'];
}
$pageTitle = 'Login - Greenfield Institute';
require_once __DIR__ . '/includes/header.php';
?>
<div class="min-h-screen flex flex-col bg-background">
<header class="sticky top-0 z-50 flex items-center px-8 h-16 bg-surface border-b border-outline-variant"><a href="<?= url('/') ?>" class="flex items-center gap-2 text-on-surface-variant hover:text-primary"><span class="ms" style="font-size:20px">arrow_back</span></a><div class="flex items-center gap-2 ml-4"><span class="ms text-primary" style="font-variation-settings:'FILL' 1">school</span><span class="font-bold text-xl text-primary">Greenfield Institute</span></div></header>
<main class="flex-grow flex items-center justify-center p-8">
<div class="w-full max-w-md bg-surface border border-border rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.05)] p-10">
  <div class="text-center mb-8"><h1 class="text-2xl font-semibold text-on-surface mb-2">Welcome Back</h1><p class="text-sm text-on-surface-variant">Sign in to access your portal</p></div>
  <?php if ($error): ?><div class="mb-4 bg-error-container text-on-error-container p-3 rounded-lg text-sm"><?= e($error) ?></div><?php endif; ?>
  <form method="POST" action="<?= url('/login.php') ?>" class="space-y-5">
    <div class="flex p-1 bg-surface-container-low rounded-lg mb-8 border border-border" id="role-toggle"><button type="button" class="flex-1 py-2 px-4 rounded-md bg-surface shadow-sm text-xs font-bold text-primary transition-all" id="role-student" onclick="setRole('student')">Student</button><button type="button" class="flex-1 py-2 px-4 rounded-md text-xs font-bold text-on-surface-variant transition-all" id="role-admin" onclick="setRole('admin')">Administrator</button></div>
    <input type="hidden" name="role" id="login-role" value="student">
    <div><label class="form-label">Email Address or Student ID</label><div class="relative"><span class="ms absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant" style="font-size:20px">person</span><input class="form-input pl-11" id="login-id" name="identifier" placeholder="e.g. GFI-9021 or john.smith@students.greenfield.edu" required type="text"></div></div>
    <div><div class="flex justify-between items-center mb-1.5"><label class="form-label" style="margin-bottom:0">Password</label></div><div class="relative"><span class="ms absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant" style="font-size:20px">lock</span><input class="form-input pl-11 pr-11" id="login-pass" name="password" placeholder="••••••••" required type="password"><button type="button" onclick="togglePass('login-pass')" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary"><span class="ms" style="font-size:20px">visibility</span></button></div></div>
    <button type="submit" class="btn-primary w-full justify-center py-3 rounded-lg mt-2">Login <span class="ms" style="font-size:18px">arrow_forward</span></button>
  </form>
  <div class="mt-6 text-center text-sm text-on-surface-variant">Don't have an account? <a href="<?= url('/register.php') ?>" class="font-bold text-primary hover:underline">Create Account</a></div>
  <div class="mt-4 p-3 bg-surface-container-low rounded-lg text-xs text-on-surface-variant"><strong class="text-on-surface">Seed demo:</strong><br>Admin: <code>admin@greenfield.edu</code> / <code>password</code><br>Student: <code>GFI-9021</code> / <code>password</code></div>
</div>
</main>
</div>
<script src="<?= asset('/assets/js/app.js') ?>"></script>
</body></html>
