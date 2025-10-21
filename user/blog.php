<?php
// blog.php
require_once __DIR__ . '/../inc/koneksi.php';
require_once __DIR__ . '/../inc/fungsi.php';

// helper URL
$home     = function_exists('url') ? url('/') : '/';
$blogList = function_exists('url') ? url('blog') : '/blog';

// Ambil slug dari query (direwrite via .htaccess)
$slug = trim($_GET['slug'] ?? '');

// ====== DETAIL ARTIKEL ======
if ($slug !== '') {
  $sql  = "SELECT id,title,slug,excerpt,content,cover_url,published,created_at,updated_at
           FROM blog_posts WHERE slug=? AND published=1 LIMIT 1";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("s", $slug);
  $stmt->execute();
  $post = $stmt->get_result()->fetch_assoc();

  if (!$post) {
    // 404 jika tidak ditemukan / belum terbit
    header("HTTP/1.1 404 Not Found");
    // kalau punya 404 khusus: header("Location: /404", true, 302); exit;
  }

    // Siapkan meta
  $title     = $post ? $post['title'] : 'Tidak ditemukan';
  $desc      = $post['excerpt'] ?: mb_substr(strip_tags($post['content'] ?? ''), 0, 140);
  $cover     = $post['cover_url'] ?: (function_exists('url') ? url('image/logo_nocapt.png') : '/image/logo_nocapt.png');
  $pubDate   = $post ? date('d M Y', strtotime($post['created_at'])) : '';
  $brand     = 'VAZATECH';
  $domain    = 'vazatech.store';
  ?>
  <!doctype html>
  <html lang="id">
  <head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title) ?> • Blog <?= htmlspecialchars($brand) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($desc) ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="../image/logo_nocapt.png" />
    <!-- Open Graph / Twitter -->
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($desc) ?>">
    <meta property="og:type" content="article">
    <meta property="og:image" content="<?= htmlspecialchars($cover) ?>">
    <meta property="og:url" content="<?= htmlspecialchars((function_exists('url')?url('blog/'.$slug):('/blog/'.$slug))) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
      :root{--bg:#0b0614;--panel:#0d0a1a;--card:#121127;--line:rgba(255,255,255,.12);
            --blue:#2e6bff;--blue2:#1a73e8;--text:#eaf0ff;--muted:#b6c3ff}
      *{box-sizing:border-box}
      body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);
        background:radial-gradient(1100px 520px at 10% -10%, rgba(46,107,255,.18),transparent 60%),
                   radial-gradient(900px 480px at 100% 0%, rgba(26,115,232,.22),transparent 55%),var(--bg)}
      .wrap{max-width:920px;margin:32px auto;padding:0 16px}
      .header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
      .brand{display:flex;align-items:center;gap:10px}
      .logo{width:42px;height:42px;object-fit:contain;border-radius:10px;background:#0f132b;padding:6px;box-shadow:0 8px 20px rgba(26,115,232,.35)}
      .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;text-decoration:none;font-weight:800;background:#1733ff;color:#fff;border:1px solid transparent;box-shadow:0 10px 22px rgba(46,107,255,.25)}
      .btn:hover{filter:brightness(1.07);transform:translateY(-1px)}
      .card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:18px}
      h1{margin:0 0 6px;font-size:30px}
      .meta{color:var(--muted);font-size:14px;margin-bottom:12px}
      .cover{width:100%;max-height:420px;object-fit:cover;border-radius:14px;border:1px solid var(--line)}
      .content{line-height:1.8;color:#dfe6ff;margin-top:14px}
      .content img{max-width:100%;height:auto;border-radius:10px}
      .content pre{background:#0f1022;padding:12px;border-radius:10px;overflow:auto}
      .back{margin-top:16px;display:flex;gap:10px;flex-wrap:wrap}
      .empty{padding:40px 16px;text-align:center;color:#b6c3ff}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="header">
        <div class="brand">
          <img class="logo" src="<?= htmlspecialchars(function_exists('url')?url('image/logo_nocapt.png'):'/image/logo_nocapt.png') ?>" alt="VAZATECH" loading="lazy">
          <div>
            <div class="meta">Blog • <?= htmlspecialchars($domain) ?></div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <div class="meta">Diterbitkan: <?= htmlspecialchars($pubDate) ?></div>
          </div>
        </div>
        <a class="btn" href="<?= htmlspecialchars($blogList) ?>">Semua Artikel</a>
      </div>

      <?php if(!$post): ?>
        <div class="card empty">Artikel tidak ditemukan atau belum diterbitkan.</div>
      <?php else: ?>
        <div class="card">
          <?php if ($cover): ?>
            <img class="cover" src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($title) ?>">
          <?php endif; ?>
          <div class="content">
            <?php
            // Jika konten disimpan sebagai HTML, tampilkan apa adanya.
            // Jika konten plain text, aktifkan nl2br:
            // echo nl2br(htmlspecialchars($post['content']));
            echo $post['content'];
            ?>
          </div>
          <div class="back">
            <a class="btn" href="<?= htmlspecialchars($blogList) ?>">Kembali ke Blog</a>
            <a class="btn" href="<?= htmlspecialchars($home) ?>">Beranda</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </body>
  </html>
  <?php
  exit;
}

// ====== (OPSIONAL) LIST ARTIKEL SAAT /blog TANPA SLUG ======
$stmt = $koneksi->prepare("SELECT title,slug,excerpt,cover_url,created_at FROM blog_posts WHERE published=1 ORDER BY created_at DESC");
$stmt->execute();
$rows = $stmt->get_result();
$brand = 'VAZATECH';
$domain = 'vazatech.store';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Blog • <?= htmlspecialchars($brand) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,follow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
  :root{
    --bg:#ffffff;          /* latar putih */
    --text:#0f172a;        /* slate-900 */
    --muted:#64748b;       /* slate-500/600 */
    --line:#e5e7eb;        /* gray-200 */
    --accent:#2563eb;      /* biru aksi */
    --tile:#ffffff;        /* warna kartu */
    --thumb:#e9ecef;       /* abu-abu thumbnail */
  }
  *{box-sizing:border-box}
  body{
    margin:0;
    font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;
    color:var(--text);
    background:var(--bg);
  }
  .wrap{max-width:980px;margin:24px auto;padding:0 16px}
  .header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}
  .brand{display:flex;align-items:center;gap:10px}
  .logo-img{width:42px;height:42px;object-fit:contain;border-radius:10px;background:#eef2ff;padding:6px;box-shadow:0 6px 16px rgba(37,99,235,.15)}
  h1{font-size:28px;margin:0}
  .meta{color:var(--muted);font-size:14px}

  /* ====== GRID TILES (seperti gambar contoh) ====== */
.tiles{
  display:grid;
  grid-template-columns:repeat(auto-fill, minmax(230px, 1fr));
  gap:22px;
}
  @media (max-width:920px){ .tiles{ grid-template-columns:repeat(3,1fr); } }
  @media (max-width:640px){ .tiles{ grid-template-columns:repeat(2,1fr); } }
  @media (max-width:420px){ .tiles{ grid-template-columns:1fr; } }

/* Link pembungkus kartu supaya seluruh kartu bisa diklik */
.tile-link{
  display:block;
  text-decoration:none;
  color:inherit;
}

  .tile{
  background:var(--tile);
  border:1px solid var(--line);
  border-radius:20px;
  padding:18px;
  transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
}
.tile:hover{
  transform:translateY(-3px);
  box-shadow:0 12px 24px rgba(15,23,42,.10);
  border-color:#dfe3ea;
}

/* Thumbnail */
.tile-thumb{               /* placeholder abu-abu (tanpa gambar) */
  height:112px;
  border-radius:16px;
  background:var(--thumb);
  position:relative;
}
.tile-thumb::after{
  content:"";
  position:absolute; inset:10px;
  border-radius:14px;
  background:#dddfe3;
}
.tile-thumb-img{           /* bila ada gambar cover */
  width:100%;
  height:112px;
  object-fit:cover;
  border-radius:16px;
  border:1px solid var(--line);
  background:#f8fafc;
}

/* Teks */
.tile-title{
  margin:12px 2px 0;
  font-size:15.5px;     /* sedikit lebih besar */
  font-weight:800;
  line-height:1.35;
}
.tile-meta{
  margin:6px 2px 0;
  font-size:12.5px;
  color:var(--muted);
}
  .tile-actions{
    margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;
  }
  /* deskripsi singkat di kartu */
.tile-desc{
  margin:8px 2px 0;
  font-size:13px;
  line-height:1.5;
  color:#334155; /* slate-700 */
  display:-webkit-box;
  -webkit-line-clamp: 3;     /* tampilkan max 3 baris */
  -webkit-box-orient: vertical;
  overflow:hidden;
}

  .btn{
    display:inline-flex;align-items:center;justify-content:center;
    padding:8px 12px;border-radius:12px;text-decoration:none;font-weight:700;
    background:var(--accent);color:#fff;border:1px solid var(--accent);
  }
  .btn:hover{ filter:brightness(1.05); }
</style>

</head>
<body>
  <div class="wrap">
    <div class="header">
      <div class="brand">
        <img class="logo-img" src="<?= htmlspecialchars(function_exists('url')?url('image/logo_nocapt.png'):'/image/logo_nocapt.png') ?>" alt="VAZATECH" loading="lazy">
        <div>
          <h1>Blog</h1>
          <div class="meta">Berita & artikel dari <?= htmlspecialchars($brand) ?></div>
        </div>
      </div>
      <a class="btn" href="<?= htmlspecialchars($home) ?>">Beranda</a>
    </div>

    <div class="card">
      <div class="tiles">
  <?php while ($r = $rows->fetch_assoc()):
        $title  = $r['title'];
        $slug   = $r['slug'];
        $date   = date('d M Y', strtotime($r['created_at']));
        $cover  = $r['cover_url'] ?: (function_exists('url')?url('image/logo_nocapt.png'):'/image/logo_nocapt.png');
        // ambil excerpt; kalau kosong, buat dari konten/teks plain di DB (jika ada kolomnya), atau dari title
        $excerpt = trim($r['excerpt'] ?? '');
        if ($excerpt === '' && isset($r['content'])) {
          $excerpt = mb_substr(strip_tags($r['content']), 0, 120) . '…';
        } elseif ($excerpt === '') {
          $excerpt = mb_substr($title, 0, 80);
        }
        $href   = function_exists('url') ? url('blog/'.$slug) : ('/blog/'.$slug);
  ?>
    <a class="tile-link" href="<?= htmlspecialchars($href) ?>">
      <article class="tile">
        <?php if ($cover): ?>
          <img class="tile-thumb-img"
               src="<?= htmlspecialchars($cover) ?>"
               alt="<?= htmlspecialchars($title) ?>"
               loading="lazy"
               onerror="this.onerror=null;this.src='<?= htmlspecialchars(function_exists('url')?url('image/logo_nocapt.png'):'/image/logo_nocapt.png') ?>'">
        <?php else: ?>
          <div class="tile-thumb" aria-hidden="true"></div>
        <?php endif; ?>

        <div class="tile-title"><?= htmlspecialchars($title) ?></div>
        <div class="tile-meta"><?= $date ?> • <?= htmlspecialchars($domain) ?></div>
        <div class="tile-desc"><?= htmlspecialchars($excerpt) ?></div>
      </article>
    </a>
  <?php endwhile; ?>

  <?php if ($rows->num_rows === 0): ?>
    <article class="tile">
      <div class="tile-title">Belum ada artikel</div>
      <div class="tile-meta">Tunggu update terbaru dari kami.</div>
      <div class="tile-desc">Kami akan segera mempublikasikan berita dan tips terbaru.</div>
    </article>
  <?php endif; ?>
</div>

    </div>
  </div>
</body>
</html>
