<?php
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) redirect($_SESSION['role'] === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php');
$pageTitle = 'Greenfield Institute – Course Registration System';
require_once __DIR__ . '/includes/header.php';
?>
<div class="min-h-screen flex flex-col bg-surface">
  <header class="sticky top-0 z-50 flex justify-between items-center px-8 h-16 bg-surface border-b border-outline-variant">
    <div class="flex items-center gap-2"><span class="ms text-primary" style="font-variation-settings:'FILL' 1">school</span><span class="font-bold text-xl text-primary">Greenfield Institute</span></div>
    <div class="flex gap-3"><a class="btn-secondary" href="<?= url('/login.php') ?>">Sign In</a><a class="btn-primary" href="<?= url('/register.php') ?>">Register</a></div>
  </header>
  <main class="flex-grow flex items-center justify-center px-8 py-16">
    <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <div class="flex flex-col gap-6">
        <h1 class="text-5xl font-bold text-primary leading-tight">Academic Registration Portal</h1>
        <p class="text-lg text-on-surface-variant max-w-md">Welcome to Greenfield Institute's centralized course registration system. Streamline enrollment with real-time seat tracking and instant confirmation.</p>
        <div class="flex flex-col gap-3 max-w-sm"><a href="<?= url('/login.php') ?>" class="btn-primary justify-center py-4 text-sm rounded-lg"><span class="ms" style="font-size:18px">login</span> Sign In</a><a href="<?= url('/register.php') ?>" class="btn-secondary justify-center py-4 text-sm rounded-lg"><span class="ms" style="font-size:18px">person_add</span>Create Student Account</a></div>
      </div>
      <div class="relative rounded-xl overflow-hidden shadow-lg border border-border aspect-square max-w-md" style="background:linear-gradient(135deg,#1b5e20 0%,#00450d 50%,#4c56af 100%);display:flex;align-items:center;justify-content:center;"><div style="text-align:center;color:white;"><span class="ms" style="font-size:96px;font-variation-settings:'FILL' 1;opacity:.9">school</span><p style="font-size:24px;font-weight:600;margin-top:16px;opacity:.95">Excellence in Education</p><p style="font-size:14px;opacity:.7;margin-top:8px">Est. 1985 · Greenfield Institute</p></div></div>
    </div>
  </main>
</div>
<script src="<?= asset('/assets/js/app.js') ?>"></script>
</body></html>
