<?php
require_once '../includes/auth.php'; require_once '../includes/functions.php'; requireAdmin(); $user=currentUser(); $courses=getCourses();
$pageTitle='XML Course Data'; $activePage='xml'; $sidebarRole='admin'; require_once '../includes/header.php'; require_once '../includes/sidebar.php';
$xml="<courses>\n"; foreach($courses as $c){$xml.="  <course code=\"".e($c['course_code'])."\">\n    <title>".e($c['course_title'])."</title>\n    <department>".e($c['department'])."</department>\n    <instructor>".e($c['instructor'])."</instructor>\n    <credits>".(int)$c['credit_hours']."</credits>\n    <availableSeats>".(int)$c['available_seats']."</availableSeats>\n  </course>\n";} $xml.="</courses>";
?>
<div class="p-8 max-w-5xl mx-auto"><div class="mb-6"><h2 class="text-2xl font-semibold mb-1">XML Course Data</h2><p class="text-sm text-on-surface-variant">Generated from the courses table.</p></div><div class="card"><pre class="xml-block"><?= $xml ?></pre></div></div>
<?php require_once '../includes/footer.php'; ?>
