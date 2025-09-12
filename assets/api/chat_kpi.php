<?php
header('Content-Type: application/json');

// dari /assets/api/ menuju /inc/
require __DIR__ . '/../../inc/koneksi.php';

/** ambil 1 angka (PDO atau mysqli) */
function scalar($sql) {
  global $pdo, $koneksi;
  try {
    if (isset($pdo)) {
      $st = $pdo->query($sql);
      return (int)($st ? $st->fetchColumn() : 0);
    }
    if (isset($koneksi)) {
      $res = $koneksi->query($sql);
      $row = $res ? $res->fetch_row() : [0];
      return (int)($row[0] ?? 0);
    }
  } catch (Throwable $e) {
    error_log('chat_kpi: '.$e->getMessage());
  }
  return 0;
}

// kalau kamu menyimpan status dalam huruf kecil ('open','pending','closed')
$open    = scalar("SELECT COUNT(*) FROM chats WHERE LOWER(status) = 'open'");
$pending = scalar("SELECT COUNT(*) FROM chats WHERE LOWER(status) = 'pending'");
$closed  = scalar("SELECT COUNT(*) FROM chats WHERE LOWER(status) = 'closed'");

// kalau suatu saat kamu simpan status angka (0/1/2) —> ubah query di atas sesuai mapping

echo json_encode(['open'=>$open, 'pending'=>$pending, 'closed'=>$closed]);


