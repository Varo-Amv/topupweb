<?php
declare(strict_types=1);

// === Header JSON & jangan keluarkan warning ke output ===
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');  // sembunyikan warning/notice di output
error_reporting(E_ALL);

// === Koneksi DB ===
// Pastikan file ini membuat $pdo (PDO) atau $koneksi (mysqli)
require_once __DIR__ . '/../../inc/koneksi.php';

// Normalisasi handle + deteksi tipe koneksi
$pdo     = $pdo     ?? null;
$koneksi = $koneksi ?? null;

$isPDO    = ($pdo     instanceof PDO);
$isMySQLi = ($koneksi instanceof mysqli);

// === Helper DB generik (PDO / mysqli) ===
function db_all(string $sql, array $params = []): array {
  global $pdo, $koneksi, $isPDO, $isMySQLi;

  try {
    if ($isPDO) {
      if ($params) {
        $st = $pdo->prepare($sql);
        $st->execute($params);
      } else {
        $st = $pdo->query($sql);
      }
      return $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    if ($isMySQLi) {
      // simple param binding untuk mysqli: ganti :name di SQL dengan nilai yang sudah di-escape
      if ($params) {
        foreach ($params as $k => $v) {
          $v = $koneksi->real_escape_string((string)$v);
          $sql = str_replace($k, "'" . $v . "'", $sql);
        }
      }
      $res = $koneksi->query($sql);
      if (!$res) return [];
      $out = [];
      while ($row = $res->fetch_assoc()) $out[] = $row;
      return $out;
    }
  } catch (Throwable $e) {
    error_log('db_all: ' . $e->getMessage());
  }
  return [];
}

function db_row(string $sql, array $params = []): array {
  $rows = db_all($sql, $params);
  return $rows[0] ?? [];
}

function db_exec(string $sql, array $params = []): bool {
  global $pdo, $koneksi, $isPDO, $isMySQLi;

  try {
    if ($isPDO) {
      $st = $pdo->prepare($sql);
      return $st->execute($params);
    }

    if ($isMySQLi) {
      if ($params) {
        foreach ($params as $k => $v) {
          $v = $koneksi->real_escape_string((string)$v);
          $sql = str_replace($k, "'" . $v . "'", $sql);
        }
      }
      return (bool)$koneksi->query($sql);
    }
  } catch (Throwable $e) {
    error_log('db_exec: ' . $e->getMessage());
  }
  return false;
}

function json_ok($data) {
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
function json_err(string $msg, int $code = 400) {
  http_response_code($code);
  echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
  exit;
}

// === Util ===
function compute_status(int $stock, int $min): string {
  if ($stock <= 0)      return 'out';
  if ($stock <= $min)   return 'low';
  return 'in';
}

// === Router aksi ===
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

if ($action === 'list') {
  $q      = trim((string)($_GET['q'] ?? ''));
  $game   = trim((string)($_GET['game'] ?? ''));
  $status = trim((string)($_GET['status'] ?? ''));

  $sql = "SELECT id, product_name, game, currency, price, stock, min_stock, status, updated_at
          FROM stocks
          WHERE 1";
  $p = [];

  if ($q !== '') {
    $sql .= " AND (product_name LIKE :q OR game LIKE :q OR currency LIKE :q)";
    $p[':q'] = "%{$q}%";
  }
  if ($game !== '') {
    $sql .= " AND game = :game";
    $p[':game'] = $game;
  }
  if ($status !== '') {
    $sql .= " AND status = :status";
    $p[':status'] = $status;
  }
  $sql .= " ORDER BY updated_at DESC, product_name ASC LIMIT 500";

  json_ok(db_all($sql, $p));
}

if ($action === 'show') {
  $id = (int)($_GET['id'] ?? 0);
  if ($id <= 0) json_err('invalid id');

  $row = db_row("SELECT id, product_name, game, currency, price, stock, min_stock, status, updated_at
                 FROM stocks WHERE id = :id", [':id' => $id]);
  if (!$row) json_err('not found', 404);
  json_ok($row);
}

if ($action === 'create' || $action === 'update') {
  $id           = (int)($_POST['id'] ?? 0);
  $product_name = trim((string)($_POST['product_name'] ?? ''));
  $game         = trim((string)($_POST['game'] ?? ''));
  $currency     = trim((string)($_POST['currency'] ?? ''));
  $price        = (int)($_POST['price']  ?? 0);
  $stock        = (int)($_POST['stock']  ?? 0);
  $min_stock    = (int)($_POST['min_stock'] ?? 0);

  if ($product_name === '' || $game === '' || $currency === '') {
    json_err('Harap lengkapi field wajib.');
  }

  $status = compute_status($stock, $min_stock);

  if ($action === 'create') {
    $ok = db_exec(
      "INSERT INTO stocks (product_name, game, currency, price, stock, min_stock, status, updated_at)
       VALUES (:product_name, :game, :currency, :price, :stock, :min_stock, :status, NOW())",
      [
        ':product_name' => $product_name,
        ':game'         => $game,
        ':currency'     => $currency,
        ':price'        => $price,
        ':stock'        => $stock,
        ':min_stock'    => $min_stock,
        ':status'       => $status
      ]
    );
    if (!$ok) json_err('gagal insert', 500);
    json_ok(['ok' => true]);
  }

  // update
  if ($id <= 0) json_err('invalid id');
  $ok = db_exec(
    "UPDATE stocks
     SET product_name = :product_name,
         game         = :game,
         currency     = :currency,
         price        = :price,
         stock        = :stock,
         min_stock    = :min_stock,
         status       = :status,
         updated_at   = NOW()
     WHERE id = :id",
    [
      ':product_name' => $product_name,
      ':game'         => $game,
      ':currency'     => $currency,
      ':price'        => $price,
      ':stock'        => $stock,
      ':min_stock'    => $min_stock,
      ':status'       => $status,
      ':id'           => $id
    ]
  );
  if (!$ok) json_err('gagal update', 500);
  json_ok(['ok' => true]);
}

if ($action === 'delete') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) json_err('invalid id');
  $ok = db_exec("DELETE FROM stocks WHERE id = :id", [':id' => $id]);
  if (!$ok) json_err('gagal delete', 500);
  json_ok(['ok' => true]);
}

json_err('unknown action', 404);
