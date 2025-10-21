<?php
// kebijakan-privasi.php
require_once __DIR__ . '/../inc/fungsi.php'; // jika ada
$home       = function_exists('url') ? url('/') : '/';
$brand      = 'VAZATECH';
$domain     = 'vazatech.store';
$contact    = 'support@vazatech.store';
$updated_at = '20 Oktober 2025'; // ubah sesuai kebutuhan
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kebijakan Privasi • <?= htmlspecialchars($brand) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,follow">
  <link rel="icon" type="image/png" sizes="32x32" href="../image/logo_nocapt.png" />
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
    @media print{body{background:#fff;color:#000}.card,.note{border:1px solid #ccc}.logo-img{background:#000}.btn,.toc{display:none}a{color:#000}}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div class="brand">
        <img class="logo-img" src="<?= htmlspecialchars(function_exists('url')?url('image/logo_nocapt.png'):'/image/logo_nocapt.png') ?>" alt="VAZATECH" loading="lazy">
        <div>
          <h1>Kebijakan Privasi</h1>
          <div class="meta">Terakhir diperbarui: <?= htmlspecialchars($updated_at) ?> • Berlaku untuk <?= htmlspecialchars($domain) ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <p>Kebijakan Privasi ini menjelaskan bagaimana <b><?= htmlspecialchars($brand) ?></b> (“kami”) mengumpulkan, menggunakan, dan melindungi data pribadi Anda saat menggunakan platform di <b><?= htmlspecialchars($domain) ?></b>.</p>

      <ul class="toc">
        <li><a href="#data-dikumpulkan">Data yang Kami Kumpulkan</a></li>
        <li><a href="#cara-penggunaan">Cara Kami Menggunakan Data</a></li>
        <li><a href="#penyimpanan">Penyimpanan & Keamanan</a></li>
        <li><a href="#pihak-ketiga">Berbagi ke Pihak Ketiga</a></li>
        <li><a href="#hak-anda">Hak Anda</a></li>
        <li><a href="#cookie">Cookie & Teknologi Serupa</a></li>
        <li><a href="#kontak">Kontak</a></li>
      </ul>
      <hr>

      <h2 id="data-dikumpulkan">Data yang Kami Kumpulkan</h2>
      <ul>
        <li>Data akun: nama, email, nomor telepon, avatar.</li>
        <li>Data transaksi: produk/top-up, nominal, metode pembayaran, riwayat.</li>
        <li>Data teknis: alamat IP, perangkat, browser, log akses.</li>
      </ul>

      <h2 id="cara-penggunaan">Cara Kami Menggunakan Data</h2>
      <ul>
        <li>Memproses pesanan dan pembayaran.</li>
        <li>Memberikan dukungan pelanggan dan notifikasi transaksi.</li>
        <li>Pencegahan penipuan, audit keamanan, dan peningkatan layanan.</li>
        <li>Pemasaran sah (dengan pilihan berhenti berlangganan).</li>
      </ul>

      <h2 id="penyimpanan">Penyimpanan & Keamanan</h2>
      <p>Data disimpan pada server/penyedia tepercaya dengan kontrol akses ketat. Kata sandi disimpan menggunakan algoritma hashing modern (mis. <code>password_hash</code>).</p>

      <h2 id="pihak-ketiga">Berbagi ke Pihak Ketiga</h2>
      <p>Kami dapat membagikan data yang diperlukan ke mitra pembayaran, penyedia top-up/publisher, layanan email, dan analitik — sebatas untuk menjalankan layanan.</p>

      <h2 id="hak-anda">Hak Anda</h2>
      <ul>
        <li>Mengakses dan memperbarui data akun.</li>
        <li>Meminta penghapusan atau penonaktifan akun sesuai ketentuan.</li>
        <li>Menarik persetujuan komunikasi pemasaran kapan saja.</li>
      </ul>

      <h2 id="cookie">Cookie & Teknologi Serupa</h2>
      <p>Kami menggunakan cookie untuk sesi login, preferensi, dan statistik. Anda dapat mengatur cookie lewat pengaturan browser, namun beberapa fitur mungkin tidak berfungsi.</p>

      <h2 id="kontak">Kontak</h2>
      <p>Pertanyaan terkait privasi: <a href="mailto:<?= htmlspecialchars($contact) ?>"><?= htmlspecialchars($contact) ?></a>.</p>

      <hr>
      <p class="note"><b>Catatan:</b> Ini template umum. Sesuaikan dengan praktik aktual (retensi data, DPO, lokasi server, dasar pemrosesan, dsb.).</p>
    </div>

    <p style="text-align:center;margin-top:16px;">
      <a class="btn" href="<?= htmlspecialchars($home) ?>">Kembali ke Beranda</a>
    </p>
  </div>
</body>
</html>
