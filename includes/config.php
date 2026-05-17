<?php
// Change this if your XAMPP folder name is different.
define('BASE_URL', '/greenfield-course-registration');

function url(string $path): string {
    return BASE_URL . $path;
}

function asset(string $path): string {
    return BASE_URL . $path;
}
?>
