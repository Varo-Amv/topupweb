<?php
// assets/api/chats.php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../inc/koneksi.php';   // pastikan menghasilkan $pdo (PDO)
if (!isset($pdo)) { http_response_code(500); echo json_encode(['error'=>'DB not ready']); exit; }

function out($data, $code=200){
  http_response_code($code);
  echo json_encode($data);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
  switch ($action) {

    /* ============  STATS KPI  ============ */
    case 'stats': {
      $sql = "SELECT status, COUNT(*) c FROM chats GROUP BY status";
      $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR); // ['open'=>N, 'pending'=>N, ...]
      out([
        'open'    => (int)($rows['open'] ?? 0),
        'pending' => (int)($rows['pending'] ?? 0),
        'closed'  => (int)($rows['closed'] ?? 0),
      ]);
    }

    /* ============  LIST CHAT  ============ */
    case 'list': {
      $q      = trim($_GET['q'] ?? '');
      $status = trim($_GET['status'] ?? '');
      $params = [];
      $where  = [];

      if ($q !== '') {
        $where[] = "(subject LIKE :q)";
        $params[':q'] = "%{$q}%";
      }
      if ($status !== '' && in_array($status, ['open','pending','closed'])) {
        $where[] = "status = :s";
        $params[':s'] = $status;
      }

      $w = $where ? ('WHERE '.implode(' AND ',$where)) : '';
      $sql = "
        SELECT c.*,
          (SELECT message   FROM chat_messages m WHERE m.chat_id=c.id ORDER BY m.id DESC LIMIT 1) AS last_message,
          (SELECT created_at FROM chat_messages m WHERE m.chat_id=c.id ORDER BY m.id DESC LIMIT 1) AS last_time
        FROM chats c
        $w
        ORDER BY COALESCE(last_time, c.created_at) DESC, c.id DESC
        LIMIT 200
      ";
      $st = $pdo->prepare($sql);
      $st->execute($params);
      out($st->fetchAll(PDO::FETCH_ASSOC));
    }

    /* ============  GET MESSAGES  ============ */
    case 'messages': {
      $chat_id = (int)($_GET['chat_id'] ?? 0);
      if ($chat_id <= 0) out(['error'=>'chat_id req'],400);

      $st = $pdo->prepare("SELECT id, chat_id, sender, message, created_at FROM chat_messages WHERE chat_id=? ORDER BY id ASC");
      $st->execute([$chat_id]);
      out($st->fetchAll(PDO::FETCH_ASSOC));
    }

    /* ============  SEND MESSAGE  ============ */
    case 'send': {
      if ($method !== 'POST') out(['error'=>'POST required'],405);

      $chat_id = (int)($_POST['chat_id'] ?? 0);
      $sender  = $_POST['sender'] ?? 'admin';
      $message = trim($_POST['message'] ?? '');

      if ($chat_id <= 0 || $message === '' || !in_array($sender,['admin','user'])) {
        out(['error'=>'invalid payload'],400);
      }

      $pdo->beginTransaction();
      $st = $pdo->prepare("INSERT INTO chat_messages (chat_id, sender, message) VALUES (?,?,?)");
      $st->execute([$chat_id, $sender, $message]);

      // update updated_at chats
      $pdo->prepare("UPDATE chats SET updated_at=NOW() WHERE id=?")->execute([$chat_id]);

      $pdo->commit();
      out(['success'=>true, 'id'=>$pdo->lastInsertId()]);
    }

    /* ============  CREATE CHAT  ============ */
    case 'create': {
      if ($method !== 'POST') out(['error'=>'POST required'],405);

      $user_id = (int)($_POST['user_id'] ?? 0);
      $subject = trim($_POST['subject'] ?? 'Percakapan baru');

      $st = $pdo->prepare("INSERT INTO chats (user_id, subject, status) VALUES (?,?, 'open')");
      $st->execute([$user_id, $subject]);
      out(['success'=>true, 'chat_id'=>$pdo->lastInsertId()]);
    }

    /* ============  SET STATUS  ============ */
    case 'set_status': {
      if ($method !== 'POST') out(['error'=>'POST required'],405);

      $chat_id = (int)($_POST['chat_id'] ?? 0);
      $status  = $_POST['status'] ?? 'open';
      if ($chat_id<=0 || !in_array($status,['open','pending','closed'])) out(['error'=>'invalid'],400);

      $st = $pdo->prepare("UPDATE chats SET status=?, updated_at=NOW() WHERE id=?");
      $st->execute([$status,$chat_id]);
      out(['success'=>true]);
    }

    default:
      out(['error'=>'unknown action'],400);
  }

} catch (Throwable $e) {
  out(['error'=>'server','detail'=>$e->getMessage()],500);
}
