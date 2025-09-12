<?php
header('Content-Type: application/json; charset=utf-8');

try {
  require dirname(__DIR__, 2) . '/../../inc/koneksi.php';

  $isPDO    = isset($pdo) && ($pdo instanceof PDO);
  $isMySQLi = isset($koneksi) && ($koneksi instanceof mysqli);

  $chat_id = isset($_GET['chat_id']) ? (int)$_GET['chat_id'] : 0;
  if ($chat_id <= 0) { throw new Exception('chat_id invalid'); }

  // meta chat
  $sqlMeta = "
    SELECT c.id, c.subject, c.status, c.user_id, c.created_at, c.updated_at,
           u.nama AS user_name, u.email AS user_email
    FROM chats c
    LEFT JOIN users u ON u.id = c.user_id
    WHERE c.id = $chat_id
    LIMIT 1
  ";
  if ($isPDO) {
    $meta = $pdo->query($sqlMeta)->fetch(PDO::FETCH_ASSOC);
  } else {
    $res = $koneksi->query($sqlMeta);
    $meta = $res ? $res->fetch_assoc() : null;
  }
  if (!$meta) throw new Exception('Chat tidak ditemukan');

  // messages
  $sqlMsg = "
    SELECT id, sender, message, created_at
    FROM chat_messages
    WHERE chat_id = $chat_id
    ORDER BY id ASC
  ";
  if ($isPDO) {
    $msgs = $pdo->query($sqlMsg)->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $res = $koneksi->query($sqlMsg);
    $msgs = [];
    if ($res) { while ($r = $res->fetch_assoc()) $msgs[] = $r; }
  }

  echo json_encode(['ok'=>true, 'meta'=>$meta, 'messages'=>$msgs], JSON_UNESCAPED_UNICODE);
} catch(Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
