<?php
if (($sidebarRole ?? '') === 'admin') require __DIR__ . '/admin_sidebar.php';
else require __DIR__ . '/student_sidebar.php';
?>
<div class="main-content">
<header class="sticky top-0 z-30 flex justify-between items-center px-8 h-16 bg-surface border-b border-outline-variant">
  <div class="flex items-center gap-3"><span class="text-sm font-semibold text-on-surface-variant hidden md:block"><?= e($pageTitle ?? 'Dashboard') ?></span></div>
  <div class="flex items-center gap-3">
    <span class="text-sm text-on-surface-variant"><?= ($sidebarRole ?? '') === 'admin' ? 'Admin' : 'Welcome' ?>: <strong class="text-on-surface"><?= e($user['full_name'] ?? 'User') ?></strong></span>
    <div class="w-9 h-9 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold text-sm"><?= e(getInitials($user['full_name'] ?? 'User')) ?></div>
  </div>
</header>
