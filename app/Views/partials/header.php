<?php
// $title peut être défini dans la vue
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= e($title ?? 'Coach Pro') ?></title>

  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
