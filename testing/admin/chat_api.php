<?php
require_once __DIR__ . '/../inc/db.php';
header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? $_POST['action'] ?? '';
function j($data){ echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
if ($action === 'list') {
  $q = trim($_GET['q'] ?? '');
  $status = trim($_GET['status'] ?? '');
  $sql = "SELECT c.*, (SELECT message FROM chat_messages WHERE chat_id=c.id ORDER BY id DESC LIMIT 1) AS last_message_text
          FROM chats c WHERE 1";
  $params = [];
  if ($q !== '') {
    $sql .= " AND (c.buyer_name LIKE ? OR c.buyer_email LIKE ? OR c.subject LIKE ? OR c.order_code LIKE ?)";
    $w = '%' . $q . '%'; array_push($params, $w, $w, $w, $w);
  }
  if ($status !== '') { $sql .= " AND c.status = ?"; $params[] = $status; }
  $sql += ""; // keep string builder
  $sql .= " ORDER BY COALESCE(c.last_message_at, c.created_at) DESC LIMIT 200";
  $stmt = $pdo->prepare($sql); $stmt->execute($params);
  $chats = $stmt->fetchAll();
  j(['chats'=>$chats]);
}
if ($action === 'messages') {
  $chat_id = (int)($_GET['chat_id'] ?? 0);
  $st = $pdo->prepare("SELECT * FROM chats WHERE id=?"); $st->execute([$chat_id]); $chat = $st->fetch();
  if(!$chat) j(['error'=>'Chat not found']);
  $msg = $pdo->prepare("SELECT id, chat_id, sender, message, created_at FROM chat_messages WHERE chat_id=? ORDER BY id ASC");
  $msg->execute([$chat_id]); $messages = $msg->fetchAll();
  j(['chat'=>$chat, 'messages'=>$messages]);
}
if ($action === 'send') {
  $chat_id = (int)($_POST['chat_id'] ?? 0);
  $message = trim($_POST['message'] ?? '');
  if ($chat_id<=0 || $message==='') j(['error'=>'Invalid params']);
  $ins = $pdo->prepare("INSERT INTO chat_messages(chat_id, sender, message) VALUES(?, 'admin', ?)");
  $ins->execute([$chat_id, $message]);
  $pdo->prepare("UPDATE chats SET last_message_at = NOW() WHERE id=?")->execute([$chat_id]);
  j(['ok'=>true]);
}
if ($action === 'update_status') {
  $chat_id = (int)($_POST['chat_id'] ?? 0);
  $status = $_POST['status'] ?? 'open';
  if (!in_array($status, ['open','pending','closed'], true)) j(['error'=>'Invalid status']);
  $pdo->prepare("UPDATE chats SET status=? WHERE id=?")->execute([$status, $chat_id]);
  j(['ok'=>true]);
}
if ($action === 'create') {
  $buyer_name = trim($_POST['buyer_name'] ?? '');
  $buyer_email = trim($_POST['buyer_email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  if ($buyer_name==='') j(['error'=>'Buyer name required']);
  $stmt = $pdo->prepare("INSERT INTO chats(buyer_name, buyer_email, subject, status, last_message_at) VALUES(?, ?, ?, 'open', NOW())");
  $stmt->execute([$buyer_name, $buyer_email, $subject]);
  $chat_id = (int)$pdo->lastInsertId();
  j(['ok'=>true, 'chat_id'=>$chat_id]);
}
j(['error'=>'Unknown action']);