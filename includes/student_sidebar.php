<aside class="sidebar" id="student-sidebar">
  <div class="flex items-center gap-3 p-6 border-b border-outline-variant">
    <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center font-bold text-on-primary-container"><?= e(getInitials($user['full_name'] ?? 'Student')) ?></div>
    <div><h2 class="text-sm font-bold text-primary">Academic Portal</h2><p class="text-xs text-on-surface-variant">Student Services</p></div>
  </div>
  <nav class="flex-1 py-4 overflow-y-auto"><ul class="space-y-1">
    <li><a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= url('/student/dashboard.php') ?>"><span class="ms" style="font-size:20px">dashboard</span>Dashboard</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'courses' ? 'active' : '' ?>" href="<?= url('/student/courses.php') ?>"><span class="ms" style="font-size:20px">search</span>Course Catalog</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'mycourses' ? 'active' : '' ?>" href="<?= url('/student/my_courses.php') ?>"><span class="ms" style="font-size:20px">app_registration</span>My Registrations</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'profile' ? 'active' : '' ?>" href="<?= url('/student/profile.php') ?>"><span class="ms" style="font-size:20px">person</span>My Profile</a></li>
  </ul></nav>
  <div class="p-4 border-t border-outline-variant"><a href="<?= url('/logout.php') ?>" class="nav-link w-full text-error hover:bg-error-container/20"><span class="ms" style="font-size:20px;color:#D32F2F">logout</span>Logout</a></div>
</aside>
