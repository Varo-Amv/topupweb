<?php 
include("./inc/koneksi.php");
// Mulai session sekali untuk halaman ini
if (session_status() !== PHP_SESSION_ACTIVE) {
  // (opsional tapi bagus)
  session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
  session_start();
}

// BACA aman (tanpa warning)
$users_email = $_SESSION['users_email'] ?? null;
$users_name  = $_SESSION['users_name']  ?? null;
// index.php
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '');

// --- LOG KUNJUNGAN HALAMAN ---
require __DIR__.'/inc/koneksi.php';
date_default_timezone_set('Asia/Jakarta'); // sesuaikan

$ip    = $_SERVER['REMOTE_ADDR']            ?? '0.0.0.0';
$ua    = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255);
$path  = substr(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/', 0, 255);
$ref   = substr($_SERVER['HTTP_REFERER'] ?? '/', 0, 255);
$today = date('Y-m-d');
$now   = date('Y-m-d H:i:s');

/* (opsional) skip bot sederhana */
$skip  = false; // <-- inisialisasi agar tidak "Undefined variable $skip"
$ua_lc = strtolower($ua);
foreach (['bot','spider','crawler','preview','facebookexternalhit','pingdom','gtmetrix'] as $b) {
  if (strpos($ua_lc, $b) !== false) { $skip = true; break; }
}

if (!$skip) {
  try {
    if (isset($pdo) && $pdo instanceof PDO) {
      // --- versi PDO ---
      $sql = "INSERT INTO access_logs (visit_date, ip, user_agent, url_path, referrer, visited_at, last_seen, hits)
              VALUES (:d,:ip,:ua,:p,:r,:n,:n,1)
              ON DUPLICATE KEY UPDATE hits = hits + 1, last_seen = VALUES(last_seen)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':d'  => $today,
        ':ip' => $ip,
        ':ua' => $ua,
        ':p'  => $path,
        ':r'  => $ref,
        ':n'  => $now,
      ]);

    } elseif (isset($koneksi) && $koneksi instanceof mysqli) {
      // --- versi MySQLi ---
      $sql = "INSERT INTO access_logs (visit_date, ip, user_agent, url_path, referrer, visited_at, last_seen, hits)
              VALUES (?,?,?,?,?,?,?,1)
              ON DUPLICATE KEY UPDATE hits = hits + 1, last_seen = VALUES(last_seen)";
      if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param('sssssss', $today, $ip, $ua, $path, $ref, $now, $now);
        $stmt->execute();
        $stmt->close();
      } else {
        error_log('mysqli prepare failed: '.$koneksi->error);
      }

    } else {
      // variabel koneksi tidak ditemukan
      error_log('DB connection not found: define $pdo (PDO) or $koneksi (mysqli) in inc/koneksi.php');
    }
  } catch (Throwable $e) {
    // supaya halaman tidak fatal jika DB error
    error_log('Log visit error: '.$e->getMessage());
  }
}
?>

<?php include("./inc/header.php"); ?>
<!DOCTYPE html>
    <!-- konten demo (hapus/ubah sesuai kebutuhan) -->
    <main class="page">
      <section class="hero-demo">
        <h1 style="color: black">Selamat datang di web VAZATECH</h1>
        <p style="color: black">Isi konten disini</p>
      </section>
    </main>
  </body>
</html>
<?php include("./inc/footer.php")?>