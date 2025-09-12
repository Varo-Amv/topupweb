<?php
require_once __DIR__ . '/../inc/db.php';

// Helpers that fail gracefully if table not found
function scalar($pdo, $sql, $params = []) {
  if (!$pdo) return 0;
  try {
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return (int) $st->fetchColumn();
  } catch (Throwable $e) {
    return 0;
  }
}

// KPI cards
$visitors_today = scalar($pdo, "SELECT COUNT(*) FROM analytics WHERE DATE(event_time)=CURDATE()", []);
$users_total    = scalar($pdo, "SELECT COUNT(*) FROM users", []);
$active_products= scalar($pdo, "SELECT COUNT(*) FROM products WHERE is_active=1", []);

// Charts data
$orders_by_day = [];
$revenue_by_day = [];
$top_games = [];

if ($pdo) {
  try {
    // last 7 days orders
    $sql = "SELECT DATE(created_at) d, COUNT(*) c FROM orders GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 7";
    $rows = $pdo->query($sql)->fetchAll();
    $orders_by_day = array_reverse($rows);

    // revenue by day
    $sql2 = "SELECT DATE(created_at) d, COALESCE(SUM(total_amount),0) s FROM orders GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 7";
    $rows2 = $pdo->query($sql2)->fetchAll();
    $revenue_by_day = array_reverse($rows2);

    // top 5 games by orders
    $sql3 = "SELECT g.name game, COUNT(*) cnt
             FROM orders o JOIN games g ON o.game_id=g.id
             GROUP BY g.name ORDER BY cnt DESC LIMIT 5";
    $top_games = $pdo->query($sql3)->fetchAll();
  } catch (Throwable $e) { /* ignore */ }
}

// Fallback sample data if DB empty
if (!$orders_by_day) {
  $orders_by_day = [
    ['d'=>date('Y-m-d', strtotime('-6 day')), 'c'=>2],
    ['d'=>date('Y-m-d', strtotime('-5 day')), 'c'=>4],
    ['d'=>date('Y-m-d', strtotime('-4 day')), 'c'=>3],
    ['d'=>date('Y-m-d', strtotime('-3 day')), 'c'=>5],
    ['d'=>date('Y-m-d', strtotime('-2 day')), 'c'=>6],
    ['d'=>date('Y-m-d', strtotime('-1 day')), 'c'=>4],
    ['d'=>date('Y-m-d'), 'c'=>7],
  ];
}
if (!$revenue_by_day) {
  $revenue_by_day = [
    ['d'=>date('Y-m-d', strtotime('-6 day')), 's'=>80000],
    ['d'=>date('Y-m-d', strtotime('-5 day')), 's'=>120000],
    ['d'=>date('Y-m-d', strtotime('-4 day')), 's'=>95000],
    ['d'=>date('Y-m-d', strtotime('-3 day')), 's'=>175000],
    ['d'=>date('Y-m-d', strtotime('-2 day')), 's'=>210000],
    ['d'=>date('Y-m-d', strtotime('-1 day')), 's'=>135000],
    ['d'=>date('Y-m-d'), 's'=>260000],
  ];
}
if (!$top_games) {
  $top_games = [
    ['game'=>'Mobile Legends', 'cnt'=>23],
    ['game'=>'Free Fire', 'cnt'=>17],
    ['game'=>'Genshin Impact', 'cnt'=>12],
    ['game'=>'PUBG Mobile', 'cnt'=>9],
    ['game'=>'Valorant', 'cnt'=>8],
  ];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>VAZATECH · Admin · Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <?php include __DIR__ . '/../partials/header.php'; ?>
  <main class="container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    <section class="content">
      <div class="dashboard-header">
        <h2>Dashboard</h2>
        <div class="cards-row">
          <div class="card kpi"><div class="kpi-title">Visitor (today)</div><div class="kpi-value"><?= number_format($visitors_today) ?></div></div>
          <div class="card kpi"><div class="kpi-title">Users</div><div class="kpi-value"><?= number_format($users_total) ?></div></div>
          <div class="card kpi"><div class="kpi-title">Active Products</div><div class="kpi-value"><?= number_format($active_products) ?></div></div>
        </div>
      </div>

      <div class="cards-row stretch">
        <div class="card large">
          <div class="card-title">Transaksi (7 hari)</div>
          <canvas id="ordersChart" height="120"></canvas>
        </div>
        <div class="card large">
          <div class="card-title">Pendapatan (7 hari)</div>
          <canvas id="revenueChart" height="120"></canvas>
        </div>
        <div class="card large">
          <div class="card-title">Penjualan per Game (Top 5)</div>
          <canvas id="gamesChart" height="120"></canvas>
        </div>
      </div>
    </section>
  </main>
  <footer class="footer"></footer>

  <script>
    const ordersLabels = <?= json_encode(array_column($orders_by_day, 'd')) ?>;
    const ordersData   = <?= json_encode(array_map('intval', array_column($orders_by_day, 'c'))) ?>;
    const revenueLabels = <?= json_encode(array_column($revenue_by_day, 'd')) ?>;
    const revenueData   = <?= json_encode(array_map('intval', array_column($revenue_by_day, 's'))) ?>;
    const gameLabels = <?= json_encode(array_column($top_games, 'game')) ?>;
    const gameData   = <?= json_encode(array_map('intval', array_column($top_games, 'cnt'))) ?>;
  </script>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
