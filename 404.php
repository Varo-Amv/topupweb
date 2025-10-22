<?php
// 404.php
http_response_code(404);
require_once __DIR__ . '/inc/fungsi.php';     // agar bisa pakai url()
$home = function_exists('url') ? url('/') : '/';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>404 • Halaman Tidak Ditemukan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/404.css">
</head>
<body>
  <div class="card">
    <div class="bar"></div>

    <div class="hero">404</div>
    <div class="title">Oops! Halaman tidak ditemukan</div>
    <div class="caption">URL yang kamu akses tidak tersedia atau sudah dipindahkan.</div>

    <div class="actions">
      <a class="btn" href="<?= htmlspecialchars($home) ?>">
        Kembali ke Beranda
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="hint">Kode kesalahan: 404 · <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES) ?></div>
  </div>
</body>
</html>
