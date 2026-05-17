<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect($_SESSION['role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = registerStudent($_POST);
    if ($result['success']) redirect('/student/dashboard.php');
    $error = $result['message'];
}
$pageTitle = 'Create Student Account';
require_once __DIR__ . '/includes/header.php';
?>
<div class="min-h-screen flex flex-col md:flex-row bg-surface-container-lowest">
<div class="hidden md:flex md:w-5/12 bg-primary relative flex-col justify-between overflow-hidden" style="background:linear-gradient(160deg,#00450d 0%,#1b5e20 40%,#4c56af 100%);"><div class="relative z-10 p-10 flex items-center gap-3"><div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg"><span class="ms text-primary" style="font-variation-settings:'FILL' 1">school</span></div><span class="text-xl font-bold text-white">Greenfield Institute</span></div><div class="relative z-10 p-10 mb-8"><h1 class="text-5xl font-bold text-white mb-5 leading-tight">Academic Excellence, Streamlined.</h1><p class="text-lg text-white/90 max-w-md">Create your student account and enroll in courses.</p></div></div>
<div class="w-full md:w-7/12 flex items-center justify-center p-8"><div class="w-full max-w-lg"><div class="mb-8"><a href="<?= url('/login.php') ?>" class="flex items-center gap-1 text-sm text-on-surface-variant hover:text-primary mb-5"><span class="ms" style="font-size:18px">arrow_back</span> Back to Login</a><h2 class="text-2xl font-semibold text-on-surface mb-1">Create Student Account</h2><p class="text-sm text-on-surface-variant">All fields are validated on the server.</p></div>
<?php if ($error): ?><div class="mb-4 bg-error-container text-on-error-container p-3 rounded-lg text-sm"><?= e($error) ?></div><?php endif; ?>
<form class="space-y-4" method="POST" action="<?= url('/register.php') ?>">
<div><label class="form-label">Full Name</label><input name="full_name" type="text" class="form-input" placeholder="Jane Doe" required minlength="3"></div>
<div class="flex flex-col sm:flex-row gap-4"><div class="flex-1"><label class="form-label">Student ID</label><input name="student_number" type="text" class="form-input" placeholder="GFI-8492" required pattern="GFI-[0-9]{4}"></div><div class="flex-1"><label class="form-label">Department</label><select name="department" class="form-input" required><option value="" disabled selected>Select Department</option><option>Computer Science</option><option>Engineering</option><option>Business Administration</option><option>Liberal Arts</option><option>Natural Sciences</option><option>Mathematics</option></select></div></div>
<div><label class="form-label">Program</label><input name="program" type="text" class="form-input" placeholder="B.S. Computer Science" required></div>
<div><label class="form-label">Institutional Email</label><input name="email" type="email" class="form-input" placeholder="jane.doe@students.greenfield.edu" required></div>
<div><label class="form-label">Password</label><div class="relative"><input name="password" type="password" class="form-input pr-11" placeholder="Min. 8 characters with letters and numbers" required minlength="8" id="reg-pass"><button type="button" onclick="togglePass('reg-pass')" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant"><span class="ms" style="font-size:20px">visibility</span></button></div><p class="text-xs text-on-surface-variant mt-1">Must be at least 8 characters and contain letters and numbers.</p></div>
<div><label class="form-label">Confirm Password</label><input name="confirm_password" type="password" class="form-input" placeholder="Re-enter password" required minlength="8"></div>
<div class="pt-2"><button type="submit" class="btn-primary w-full justify-center py-3 rounded-lg">Register Account <span class="ms" style="font-size:18px">arrow_forward</span></button></div>
</form></div></div></div>
<script src="<?= asset('/assets/js/app.js') ?>"></script>
</body></html>
