<?php
header('Content-Type: application/json; charset=utf-8');

try {
  require dirname(__DIR__, 2) . '/inc/koneksi.php'; // ../.. dari assets/api

  // Deteksi koneksi (PDO / mysqli)
  $isPDO    = isset($pdo) && ($pdo instanceof PDO);
  $isMySQLi = isset($koneksi) && ($koneksi instanceof mysqli);

  $q      = isset($_GET['q']) ? trim($_GET['q']) : '';
  $status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
  $limit  = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 50;

  // WHERE kondisi
  $where = [];
  if ($status !== 'all' && in_array($status, ['open','pending','closed'])) {
    $where[] = "c.status = '" . ($isPDO ? addslashes($status) : $koneksi->real_escape_string($status)) . "'";
  }
  if ($q !== '') {
    $qLike = ($isPDO ? "%$q%" : '%' . $koneksi->real_escape_string($q) . '%');
    $where[] = "(c.subject LIKE " . ($isPDO ? $pdo->quote($qLike) : "'" . $qLike . "'") . "
                OR u.nama LIKE " . ($isPDO ? $pdo->quote($qLike) : "'" . $qLike . "'") . "
                OR u.email LIKE " . ($isPDO ? $pdo->quote($qLike) : "'" . $qLike . "'") . ")";
  }
  $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

  $sql = "
    SELECT c.id, c.subject, c.status, c.updated_at,
           u.nama AS user_name, u.email AS user_email,
           ( SELECT m.message FROM chat_messages m
             WHERE m.chat_id = c.id ORDER BY m.id DESC LIMIT 1 ) AS last_message,
           ( SELECT m.created_at FROM chat_messages m
             WHERE m.chat_id = c.id ORDER BY m.id DESC LIMIT 1 ) AS last_at
    FROM chats c
    LEFT JOIN users u ON u.id = c.user_id
    $whereSql
    ORDER BY c.updated_at DESC
    LIMIT $limit
  ";

  if ($isPDO) {
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $res = $koneksi->query($sql);
    $rows = [];
    if ($res) { while ($r = $res->fetch_assoc()) $rows[] = $r; }
  }

  echo json_encode(['ok'=>true, 'rows'=>$rows], JSON_UNESCAPED_UNICODE);
} catch(Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$e->getMessage()]);
}
