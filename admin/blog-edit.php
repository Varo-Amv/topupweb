<?php
// admin/blog_edit.php
require_once __DIR__ . '/../inc/session.php';
require_once __DIR__ . '/../inc/koneksi.php';
require_once __DIR__ . '/../inc/fungsi.php';
require_once __DIR__ . '/../inc/auth.php';
require_role(['admin','staff']); // hanya admin/staff

// CSRF
if (empty($_SESSION['csrf_blog'])) { $_SESSION['csrf_blog'] = bin2hex(random_bytes(32)); }
$csrf = $_SESSION['csrf_blog'];

// Konfigurasi upload (sama seperti create)
$USE_IMGBB = false;  $IMGBB_KEY = '';
$LOCAL_DIR = __DIR__ . '/../assets/uploads/blog';
$LOCAL_URL = (function_exists('url') ? url('assets/uploads/blog') : '/assets/uploads/blog');

// Ambil artikel by slug atau id
$slug = trim($_GET['slug'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

if ($slug !== '') {
  $stmt = $koneksi->prepare("SELECT * FROM blog_posts WHERE slug=? LIMIT 1");
  $stmt->bind_param("s", $slug);
} else {
  $stmt = $koneksi->prepare("SELECT * FROM blog_posts WHERE id=? LIMIT 1");
  $stmt->bind_param("i", $id);
}
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) { header('HTTP/1.1 404 Not Found'); exit('Artikel tidak ditemukan.'); }

$title     = $post['title'];
$excerpt   = $post['excerpt'];
$content   = $post['content'];
$published = (int)$post['published'];
$cover_url = $post['cover_url'];

$msg=''; $ok=false;

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!hash_equals($_SESSION['csrf_blog'], $_POST['csrf'] ?? '')) {
    $msg = 'Sesi berakhir. Muat ulang halaman.';
  } else {
    $title     = trim($_POST['title'] ?? '');
    $excerpt   = trim($_POST['excerpt'] ?? '');
    $content   = trim($_POST['content'] ?? '');
    $published = isset($_POST['published']) ? 1 : 0;
    $remove_cover = isset($_POST['remove_cover']);

    if ($title==='')         $msg = "Judul wajib diisi.";
    elseif (strlen($content)<20) $msg = "Konten terlalu pendek (min 20 karakter).";

    // handle cover
    $new_cover = $cover_url;
    if ($msg==='') {
      if ($remove_cover) {
        // hapus file lokal bila path lokal
        if ($cover_url && str_starts_with($cover_url, (function_exists('url')?url('assets/uploads/blog'):'/assets/uploads/blog'))) {
          $local = __DIR__.'/..'.parse_url($cover_url, PHP_URL_PATH);
          if (is_file($local)) @unlink($local);
        }
        $new_cover = null;
      }
      if (!empty($_FILES['cover']['tmp_name'])) {
        if ($USE_IMGBB && function_exists('upload_to_imgbb') && $IMGBB_KEY) {
          $res = upload_to_imgbb($_FILES['cover']['tmp_name'], $_FILES['cover']['name'], $IMGBB_KEY);
          if ($res['ok']) { $new_cover = $res['url']; }
          else { $msg = "Upload cover gagal: ".$res['err']; }
        } else {
          if (!is_dir($LOCAL_DIR)) { @mkdir($LOCAL_DIR,0775,true); }
          $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
          if (!in_array($ext,['jpg','jpeg','png','webp'])) $ext = 'jpg';
          $basename = date('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$ext;
          $dest = rtrim($LOCAL_DIR,'/').'/'.$basename;
          if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
            // hapus lama jika lokal
            if ($cover_url && str_starts_with($cover_url, (function_exists('url')?url('assets/uploads/blog'):'/assets/uploads/blog'))) {
              $local = __DIR__.'/..'.parse_url($cover_url, PHP_URL_PATH);
              if (is_file($local)) @unlink($local);
            }
            $new_cover = rtrim($LOCAL_URL,'/').'/'.$basename;
          } else {
            $msg = 'Gagal menyimpan file cover.';
          }
        }
      }
    }

    if ($msg==='') {
      $sql = "UPDATE blog_posts SET title=?, excerpt=?, content=?, cover_url=?, published=?, updated_at=NOW() WHERE id=?";
      $u = $koneksi->prepare($sql);
      $u->bind_param("ssssii", $title, $excerpt, $content, $new_cover, $published, $post['id']);
      $ok = $u->execute();
      $msg = $ok ? 'Perubahan disimpan.' : 'Gagal menyimpan perubahan.';
      if ($ok) { $cover_url = $new_cover; } // sync tampilan
    }
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Artikel • Admin • VAZATECH</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="icon" type="image/png" sizes="32x32" href="../image/logo_nocapt.png" />
  <style>
    :root{--bg:#0b0614;--card:#121127;--line:rgba(255,255,255,.12);--text:#eaf0ff}
    *{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--text)}
    .wrap{max-width:980px;margin:24px auto;padding:0 16px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:18px}
    h1{margin:6px 0 14px}
    label{font-weight:600;margin-top:12px;display:block}
    .input,.area{width:100%;padding:10px 12px;border-radius:10px;border:1px solid var(--line);background:#0f1022;color:#eaf0ff}
    .area{min-height:220px;resize:vertical;line-height:1.6}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .thumb{max-width:100%;border-radius:10px;border:1px solid var(--line)}
    .actions{display:flex;gap:10px;margin-top:16px;flex-wrap:wrap}
    .btn{appearance:none;border:0;border-radius:10px;padding:10px 14px;font-weight:800;cursor:pointer;text-decoration:none}
    .btn-primary{background:#1733ff;color:#fff}
    .btn-ghost{background:#14142b;color:#dfe6ff;border:1px solid var(--line)}
    .note{margin:10px 0;padding:10px 12px;border-radius:10px}
    .ok{background:#0d2f1f;border:1px solid #1c6b3d} .err{background:#2b1416;border:1px solid #6b1c22}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Edit Artikel</h1>

    <?php if ($msg): ?>
      <div class="note <?= $ok?'ok':'err' ?>"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

        <label>Judul</label>
        <input class="input" type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>

        <div class="row">
          <div>
            <label>Cover (unggah baru untuk mengganti)</label>
            <input class="input" type="file" name="cover" accept="image/*">
          </div>
          <div>
            <label style="display:flex;align-items:center;gap:8px;margin-top:34px">
              <input type="checkbox" name="published" <?= $published? 'checked':'' ?>> Terbitkan
            </label>
            <?php if ($cover_url): ?>
              <label style="display:flex;align-items:center;gap:8px;margin-top:10px">
                <input type="checkbox" name="remove_cover"> Hapus cover sekarang
              </label>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($cover_url): ?>
          <div style="margin-top:10px">
            <img class="thumb" src="<?= htmlspecialchars($cover_url) ?>" alt="Cover">
          </div>
        <?php endif; ?>

        <label>Ringkasan (opsional)</label>
        <textarea class="area" name="excerpt"><?= htmlspecialchars($excerpt) ?></textarea>

        <label>Konten</label>
        <textarea class="area" name="content" required><?= htmlspecialchars($content) ?></textarea>

        <div class="actions">
          <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
          <a class="btn btn-ghost" href="blog-list">Kembali ke Daftar</a>
          <a class="btn btn-ghost" target="_blank" href="<?= htmlspecialchars((function_exists('url')?url('blog/'.$post['slug']):('/blog/'.$post['slug']))) ?>">Lihat Publik</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
