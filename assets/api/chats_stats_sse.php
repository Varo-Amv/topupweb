<?php
// assets/api/chat_stats_sse.php
// Realtime KPI via Server-Sent Events (SSE)

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
// header('Access-Control-Allow-Origin: *'); // aktifkan jika butuh CORS

// pastikan tidak time out
@set_time_limit(0);
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', 0);

require __DIR__ . '/../../inc/koneksi.php'; // harus menghasilkan $pdo (PDO)

if (!isset($pdo)) {
  echo "event: error\ndata: " . json_encode(['error'=>'DB not ready']) . "\n\n";
  flush(); exit;
}

function kpi_get($pdo): array {
  $rows = $pdo->query("SELECT status, COUNT(*) c FROM chats GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
  return [
    'open'    => (int)($rows['open'] ?? 0),
    'pending' => (int)($rows['pending'] ?? 0),
    'closed'  => (int)($rows['closed'] ?? 0),
    'ts'      => time()
  ];
}

ignore_user_abort(true);

$lastSig = null;
$keepAliveTick = 0;

while (!connection_aborted()) {
  try {
    $stats = kpi_get($pdo);
    $sig   = md5(json_encode($stats));

    // kirim hanya jika berubah
    if ($sig !== $lastSig) {
      echo "event: stats\n";
      echo "data: " . json_encode($stats) . "\n\n";
      $lastSig = $sig;
      @ob_flush(); flush();
    }

    // keep-alive setiap 20 detik biar koneksi tidak ditutup proxy
    if (++$keepAliveTick >= 20/2) {
      echo ": ping\n\n"; // komentar SSE
      $keepAliveTick = 0;
      @ob_flush(); flush();
    }

    sleep(2); // cek setiap 2 detik
  } catch (Throwable $e) {
    echo "event: error\n";
    echo "data: " . json_encode(['error'=>$e->getMessage()]) . "\n\n";
    @ob_flush(); flush();
    sleep(3);
  }
}
