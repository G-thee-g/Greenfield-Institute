<?php
require_once __DIR__ . '/config.php';
if (!isset($pageTitle)) $pageTitle = 'Greenfield Institute – Course Registration System';
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
tailwind.config = {
  darkMode: "class",
  theme: { extend: {
    colors: {
      "outline": "#717a6d", "surface-container-lowest": "#ffffff", "inverse-primary": "#91d78a", "on-error": "#ffffff",
      "secondary-container": "#959efd", "surface-variant": "#e2e2e2", "surface-tint": "#2a6b2c", "primary-fixed-dim": "#91d78a",
      "inverse-on-surface": "#f1f1f1", "surface-bright": "#f9f9f9", "secondary-fixed": "#e0e0ff", "on-secondary-fixed-variant": "#343d96",
      "tertiary": "#004245", "on-background": "#1a1c1c", "primary-container": "#1b5e20", "secondary-fixed-dim": "#bdc2ff",
      "outline-variant": "#c0c9bb", "background": "#f9f9f9", "border": "#E0E0E0", "on-surface": "#1a1c1c", "primary": "#00450d",
      "on-primary-container": "#90d689", "surface-container": "#eeeeee", "on-tertiary": "#ffffff", "error": "#D32F2F",
      "surface-container-highest": "#e2e2e2", "secondary": "#4c56af", "primary-fixed": "#acf4a4", "on-secondary-fixed": "#000767",
      "on-surface-variant": "#41493e", "tertiary-container": "#005b5f", "surface-dim": "#dadada", "on-secondary": "#ffffff",
      "success": "#2E7D32", "on-primary": "#ffffff", "on-primary-fixed-variant": "#0c5216", "surface-container-high": "#e8e8e8",
      "on-primary-fixed": "#002203", "info": "#0288D1", "on-secondary-container": "#27308a", "surface": "#FFFFFF",
      "on-error-container": "#93000a", "inverse-surface": "#2f3131", "warning": "#ED6C02", "error-container": "#ffdad6", "surface-container-low": "#f3f3f3"
    },
    fontFamily: { sans: ["Inter", "sans-serif"] },
    borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" }
  }}
}
</script>
<link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">
</head>
<body class="bg-background text-on-surface antialiased">
