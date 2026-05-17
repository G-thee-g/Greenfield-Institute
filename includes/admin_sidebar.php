<aside class="sidebar" id="admin-sidebar">
  <div class="flex items-center gap-3 p-6 border-b border-outline-variant">
    <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold"><?= e(getInitials($user['full_name'] ?? 'Admin')) ?></div>
    <div><h2 class="text-sm font-bold text-primary">Admin Panel</h2><p class="text-xs text-on-surface-variant">Academic Administration</p></div>
  </div>
  <nav class="flex-1 py-4 overflow-y-auto"><ul class="space-y-1">
    <li><a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= url('/admin/dashboard.php') ?>"><span class="ms" style="font-size:20px">dashboard</span>Dashboard</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'courses' ? 'active' : '' ?>" href="<?= url('/admin/manage_courses.php') ?>"><span class="ms" style="font-size:20px">library_books</span>Manage Courses</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'addcourse' ? 'active' : '' ?>" href="<?= url('/admin/add_course.php') ?>"><span class="ms" style="font-size:20px">add_circle</span>Add Course</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'registrations' ? 'active' : '' ?>" href="<?= url('/admin/registrations.php') ?>"><span class="ms" style="font-size:20px">how_to_reg</span>Registrations</a></li>
    <li><a class="nav-link <?= ($activePage ?? '') === 'xml' ? 'active' : '' ?>" href="<?= url('/admin/xml_data.php') ?>"><span class="ms" style="font-size:20px">code</span>XML Data</a></li>
  </ul></nav>
  <div class="p-4 border-t border-outline-variant"><a href="<?= url('/logout.php') ?>" class="nav-link w-full text-error hover:bg-error-container/20"><span class="ms" style="font-size:20px;color:#D32F2F">logout</span>Logout</a></div>
</aside>
