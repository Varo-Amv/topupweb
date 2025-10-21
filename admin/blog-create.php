<?php
// admin/blog_create.php — Tema putih
require_once __DIR__ . '/../inc/session.php';
require_once __DIR__ . '/../inc/koneksi.php';
require_once __DIR__ . '/../inc/fungsi.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/env.php';
require_role(['admin','staff']); // hanya admin/staff

// ---- CSRF ----
if (empty($_SESSION['csrf_blog'])) {
  $_SESSION['csrf_blog'] = bin2hex(random_bytes(32));
}
function csrf_ok($t){ return isset($_SESSION['csrf_blog']) && hash_equals($_SESSION['csrf_blog'], $t ?? ''); }

// === Konfigurasi upload cover ===
$USE_IMGBB = true;                                    // true jika pakai ImgBB
$IMGBB_KEY = $KeyGBB;      // API key ImgBB kamu

// (Jika ingin fallback lokal, buka komentar ini)
// $LOCAL_DIR = __DIR__ . '/../assets/uploads/blog';
// $LOCAL_URL = (function_exists('url') ? url('assets/uploads/blog') : '/assets/uploads/blog');

// ---- helper slug ----
function slugify($text){
  $text = strtolower($text);
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = preg_replace('~[^-\w]+~', '', $text);
  if (empty($text)) $text = 'post';
  return $text;
}

// ---- inisialisasi form ----
$title=''; $excerpt=''; $content=''; $published=1;
$msg=''; $ok=false;

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_ok($_POST['csrf'] ?? '')) {
    $msg = "Sesi berakhir. Muat ulang halaman.";
  } else {
    $title     = trim($_POST['title'] ?? '');
    $excerpt   = trim($_POST['excerpt'] ?? '');
    $content   = trim($_POST['content'] ?? '');
    $published = isset($_POST['published']) ? 1 : 0;

    if ($title==='')              $msg = "Judul wajib diisi.";
    elseif (strlen($content)<20)  $msg = "Konten terlalu pendek (min 20 karakter).";
    else {
      // slug unik
      $slug = slugify($title);
      $cek  = $koneksi->prepare("SELECT COUNT(*) c FROM blog_posts WHERE slug=?");
      $slugTry = $slug; $i=2;
      while (true) {
        $cek->bind_param("s", $slugTry);
        $cek->execute();
        $c = $cek->get_result()->fetch_assoc()['c'] ?? 0;
        if ($c==0) { $slug = $slugTry; break; }
        $slugTry = $slug.'-'.$i++;
      }

      // cover (opsional)
      $cover_url = null;
      if (!empty($_FILES['cover']['tmp_name'])) {
        if ($USE_IMGBB && function_exists('upload_to_imgbb') && $IMGBB_KEY) {
          $res = upload_to_imgbb($_FILES['cover']['tmp_name'], $_FILES['cover']['name'], $IMGBB_KEY);
          if ($res['ok']) { $cover_url = $res['url']; }
          else { $msg = "Upload cover gagal: ".$res['err']; }
        } else {
          // --- fallback penyimpanan lokal (buka komentar jika dipakai) ---
          // if (!is_dir($LOCAL_DIR)) { @mkdir($LOCAL_DIR,0775,true); }
          // $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
          // if (!in_array($ext,['jpg','jpeg','png','webp'])) $ext = 'jpg';
          // $basename = date('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$ext;
          // $dest = rtrim($LOCAL_DIR,'/').'/'.$basename;
          // if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
          //   $cover_url = rtrim($LOCAL_URL,'/').'/'.$basename;
          // } else {
          //   $msg = "Gagal menyimpan file cover.";
          // }
          $msg = "Upload cover dinonaktifkan (fallback lokal tidak diaktifkan).";
        }
      }

      if ($msg==='') {
        $sql = "INSERT INTO blog_posts (title, slug, excerpt, content, cover_url, published, author_id, created_at, updated_at)
                VALUES (?,?,?,?,?,?,?,NOW(),NOW())";
        $stmt = $koneksi->prepare($sql);
        $author = (int)($_SESSION['user']['id'] ?? 0);
        $stmt->bind_param("sssssis", $title, $slug, $excerpt, $content, $cover_url, $published, $author);
        if ($stmt->execute()) {
          $ok  = true;
          $msg = "Artikel berhasil dibuat.";
          header("Location: blog-edit?slug=".$slug);
          exit;
        } else {
          $msg = "Gagal menyimpan artikel. Coba lagi.";
        }
      }
    }
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Buat Artikel • Admin • VAZATECH</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8fb;
      --panel:#ffffff;
      --line:#e6eaf2;
      --text:#0f172a;
      --muted:#64748b;
      --primary:#2563eb;
      --chip:#1118270d;
    }
    *{box-sizing:border-box}
    html,body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif}
    .wrap{max-width:1040px;margin:26px auto;padding:0 18px}

    .page-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px}
    .page-head h1{margin:0;font-size:26px}
    .badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#1f2937;background:#eef2ff;border:1px solid #c7d2fe;padding:6px 10px;border-radius:999px}

    .card{background:var(--panel);border:1px solid var(--line);border-radius:16px;box-shadow:0 10px 22px rgba(15,23,42,.06);overflow:hidden}
    .card-head{padding:14px 16px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px;background:#fbfdff}
    .card-body{padding:16px}

    .grid{display:grid;grid-template-columns:2fr 1fr;gap:16px}
    @media (max-width:900px){ .grid{grid-template-columns:1fr} }

    label{font-weight:700;margin:10px 0 6px;display:block}
    .input,.area{
      width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;color:var(--text);
      transition:border-color .15s, box-shadow .15s;
    }
    .input:focus,.area:focus{outline:none;border-color:#c7d2fe;box-shadow:0 0 0 4px #e0e7ff}
    .area{min-height:190px;resize:vertical;line-height:1.65}

    .helper{font-size:12px;color:var(--muted);margin-top:6px}
    .muted{color:var(--muted);font-size:13px}

    .actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
    /* Vertikal di mobile */
    @media (max-width:768px){
      .actions{flex-direction:column}
      .actions .btn{width:100%;justify-content:center}
    }

    .btn{
      appearance:none;border:1px solid transparent;border-radius:12px;padding:10px 14px;font-weight:800;cursor:pointer;text-decoration:none;
      display:inline-flex;align-items:center;gap:8px;transition:filter .15s, transform .15s;
    }
    .btn-primary{background:var(--primary);border-color:var(--primary);color:#fff}
    .btn-primary:hover{filter:brightness(1.05);transform:translateY(-1px)}
    .btn-ghost{background:#fff;border-color:var(--line);color:var(--text)}
    .btn-ghost:hover{border-color:#cdd4e0}

    .note{margin:14px 0;padding:12px 14px;border-radius:12px;border:1px solid var(--line);background:#fff}
    .note.ok{border-color:#bbf7d0;background:#ecfdf5;color:#065f46}
    .note.err{border-color:#fecaca;background:#fef2f2;color:#7f1d1d}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="page-head">
      <h1>Buat Artikel Blog</h1>
      <span class="badge">Draft Baru</span>
    </div>

    <?php if ($msg): ?>
      <div class="note <?= $ok?'ok':'err' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head">
        <div class="muted">Isi judul, konten, dan (opsional) cover. Centang “Terbitkan sekarang” jika ingin langsung live.</div>
      </div>

      <div class="card-body">
        <form method="post" enctype="multipart/form-data" autocomplete="off">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_blog']) ?>">

          <div class="grid">
            <!-- KIRI -->
            <div>
              <label>Judul</label>
              <input class="input" type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>

              <label>Ringkasan (opsional)</label>
              <textarea class="area" name="excerpt" placeholder="Ringkasan singkat (ditampilkan di daftar blog)"><?= htmlspecialchars($excerpt) ?></textarea>

              <label>Konten</label>
              <textarea class="area" name="content" required placeholder="Tulis konten artikel di sini..."><?= htmlspecialchars($content) ?></textarea>
              <div class="helper">Tip: bisa menempel HTML/gambar. Gunakan &lt;pre&gt; untuk kode.</div>
            </div>

            <!-- KANAN -->
            <div>
              <label>Cover (opsional)</label>
              <input class="input" type="file" name="cover" accept="image/*">
              <div class="helper">PNG/JPG/WEBP disarankan ≤ 1MB. Saat ini upload via ImgBB.</div>

              <label style="display:flex;align-items:center;gap:10px;margin-top:12px">
                <input type="checkbox" name="published" <?= $published? 'checked':'' ?>> Terbitkan sekarang
              </label>

              <div class="actions" style="margin-top:14px">
                <button class="btn btn-primary" type="submit">Simpan Artikel</button>
                <a class="btn btn-ghost" href="blog-list">Kembali ke Daftar</a>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</body>
</html>
