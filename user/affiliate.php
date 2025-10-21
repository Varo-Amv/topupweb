<?php
// affiliate.php
require_once __DIR__ . '/../inc/fungsi.php';
$home       = function_exists('url') ? url('/') : '/';
$brand      = 'VAZATECH';
$domain     = 'vazatech.store';
$contact    = 'support@vazatech.store';
$updated_at = '20 Oktober 2025';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Program Affiliate • <?= htmlspecialchars($brand) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,follow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0614;--panel:#0d0a1a;--card:#121127;--blue:#2e6bff;--blue2:#1a73e8;--text:#eaf0ff;--muted:#b6c3ff;--line:rgba(255,255,255,.08)}
    *{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);
    background:radial-gradient(1100px 520px at 10% -10%, rgba(46,107,255,.18),transparent 60%),
               radial-gradient(900px 480px at 100% 0%, rgba(26,115,232,.22),transparent 55%),var(--bg)}
    .wrap{max-width:980px;margin:32px auto;padding:0 16px}
    .header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
    .brand{display:flex;align-items:center;gap:10px}
    .logo-img{width:42px;height:42px;object-fit:contain;border-radius:10px;background:#0f132b;padding:6px;box-shadow:0 8px 20px rgba(26,115,232,.35)}
    h1{font-size:28px;margin:0}.meta{color:var(--muted);font-size:14px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:20px}
    .toc{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 12px 0;padding:0;list-style:none}
    .toc a{display:inline-block;padding:8px 12px;border:1px solid var(--line);border-radius:999px;color:var(--text);text-decoration:none}
    .toc a:hover{border-color:var(--blue)}
    h2{font-size:20px;margin:22px 0 8px}p,li{line-height:1.7;color:#dfe6ff}
    .note{background:#0e1633;border:1px solid var(--line);padding:12px 14px;border-radius:12px;color:#cfdbff;margin:12px 0}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;text-decoration:none;font-weight:800;background:#1733ff;color:#fff;border:1px solid transparent;box-shadow:0 10px 22px rgba(46,107,255,.25)}
    .btn:hover{filter:brightness(1.07);transform:translateY(-1px)}
    hr{border:0;height:1px;background:var(--line);margin:18px 0}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div class="brand">
        <img class="logo-img" src="<?= htmlspecialchars(function_exists('url')?url('image/logo_nocapt.png'):'/image/logo_nocapt.png') ?>" alt="VAZATECH" loading="lazy">
        <div>
          <h1>Program Affiliate</h1>
          <div class="meta">Terakhir diperbarui: <?= htmlspecialchars($updated_at) ?> • Berlaku untuk <?= htmlspecialchars($domain) ?></div>
        </div>
      </div>
      <a class="btn" href="<?= htmlspecialchars($home) ?>">Kembali ke Beranda</a>
    </div>

    <div class="card">
      <p>Dengan bergabung pada Program Affiliate <b><?= htmlspecialchars($brand) ?></b>, Anda (“Affiliate”) menyetujui ketentuan berikut.</p>

      <ul class="toc">
        <li><a href="#pendaftaran">Pendaftaran</a></li>
        <li><a href="#tautan">Tautan & Kode Referral</a></li>
        <li><a href="#komisi">Komisi & Pembayaran</a></li>
        <li><a href="#larangan">Larangan</a></li>
        <li><a href="#penghentian">Penghentian</a></li>
        <li><a href="#perubahan">Perubahan Program</a></li>
        <li><a
