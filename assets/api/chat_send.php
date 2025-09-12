<?php
header('Content-Type: application/json; charset=utf-8');

try {
  require dirname(__DIR__, 2) .' /../../inc/koneksi.php';
  date_default_timezone_set('Asia/Jakarta');

  $isPDO    = isset($pdo) && ($pdo instanceof PDO);
  $isMySQLi = isset($koneksi) && ($koneksi instanceof mysqli);

  // Ambil POST
  $chat_id = isset($_POST['chat_id']) ? (int)$_POST['chat_id'] : 0;
  $msg     = isset($_POST['message']) ? trim($_POST['message']) : '';
  $sender  = 'admin'; // halaman admin

  if ($chat_id <= 0 || $msg === '') throw new Exception('Data kurang');
  $now = date('Y-m-d H:i:s');

  if ($isPDO) {
    $st = $pdo->prepare("INSERT INTO chat_messages (chat_id, sender, message, created_at) VALUES (?, ?, ?, ?)");
    $st->execute([$chat_id, $sender, $msg, $now]);

    $st2 = $pdo->prepare("UPDATE chats SET updated_at = ? WHERE id = ?");
    $st2->execute([$now, $chat_id]);
  } else {
    $chat_id = (int)$chat_id;
    $msgEsc  = $koneksi->real_escape_string($msg);
    $sender  = $koneksi->real_escape_string($sender);
    $nowEsc  = $koneksi->real_escape_string($now);

    $koneksi->query("INSERT INTO chat_messages(chat_id, sender, message, created_at)
                     VALUES ($chat_id, '$sender', '$msgEsc', '$nowEsc')");
    $koneksi->query("UPDATE chats SET updated_at='$nowEsc' WHERE id=$chat_id");
  }

  echo json_encode(['ok'=>true]);
} catch(Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
