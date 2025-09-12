<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VAZATECH Admin · Orders</title>

    <!-- Font & Icons -->
    <link
      href="https://fonts.googleapis.com/css2?family=Lexend+Tera:wght@100..900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />

    <!-- Pakai admin.css utama (tambahkan snippet CSS yang aku kirim di bawah) -->
    <link rel="stylesheet" href="../assets/css/admin.css" />
  </head>
  <body>
    <!-- Header -->
    <header>
      <!-- Logo di kiri -->
      <div class="logo">
        <img src="../image/logo_nocapt.png" alt="Logo" />
        <span class="logo">V A Z A T E C H</span>
      </div>

      <!-- Icon profile di kanan -->
      <div class="profile">
        <a href="../#"
          ><img src="../image/profile_white.png" alt="Profile"
        /></a>
      </div>
    </header>

    <main class="container">
      <!-- Sidebar -->
      <aside class="sidebar">
        <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
        <a href="chats.php"><i class="fas fa-comments"></i>Chats</a>
        <a href="stocks.php"><i class="fas fa-box"></i>Stocks</a>
        <a href="users.php"><i class="fas fa-users"></i>Users</a>
        <a href="#" class="active"
          ><i class="fas fa-shopping-cart"></i>Orders</a
        >
      </aside>

      <!-- Content -->
      <section class="content">
        <!-- Header + KPI -->
        <div class="dashboard-header">
          <h2>Orders</h2>
          <div class="cards-row">
            <div class="card kpi">
              <div class="kpi-title">Orders Today</div>
              <div class="kpi-value">27</div>
            </div>
            <div class="card kpi">
              <div class="card kpi kpi--revenue">
                <div class="kpi-title">Revenue Today</div>
                <div class="kpi-value">
                  <span class="currency">Rp</span
                  ><span class="amount">3,420,000</span>
                </div>
              </div>
            </div>
            <div class="card kpi">
              <div class="kpi-title">Pending Payments</div>
              <div class="kpi-value">4</div>
            </div>
          </div>
        </div>

        <!-- Panel utama: Toolbar + Tabel Orders -->
        <div class="card panel">
          <div class="panel-toolbar">
            <div class="left">
              <div class="control">
                <i class="fa fa-magnifying-glass"></i>
                <input
                  type="text"
                  class="input"
                  placeholder="Cari kode / nama / email / game / produk"
                />
              </div>
              <input type="date" class="input input-date" value="2025-08-24" />
              <span class="sep">—</span>
              <input type="date" class="input input-date" value="2025-08-28" />
              <select class="select">
                <option value="">Semua Status</option>
                <option>pending</option>
                <option>paid</option>
                <option>processed</option>
                <option>success</option>
                <option>failed</option>
                <option>expired</option>
                <option>cancelled</option>
              </select>
              <select class="select">
                <option value="">Semua Channel</option>
                <option>QRIS</option>
                <option>OVO</option>
                <option>GoPay</option>
                <option>DANA</option>
                <option>BCA VA</option>
              </select>
            </div>
            <div class="right">
              <button class="btn btn-secondary">
                <i class="fa fa-file-export"></i> Export CSV
              </button>
              <button class="btn btn-primary">
                <i class="fa fa-plus"></i> Buat Order
              </button>
            </div>
          </div>

          <div class="table-wrap">
            <div class="scroll-inner">
              <table class="orders-table">
                <thead>
                  <tr>
                    <th>Kode</th>
                    <th>Pembeli</th>
                    <th>Game / Produk</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Update</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><code class="mono">ABC12345</code></td>
                    <td>
                      <div class="user-cell">
                        <div class="avatar">RP</div>
                        <div class="meta">
                          <div class="name">Raka Pratama</div>
                          <div class="sub">raka@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Mobile Legends — 172 Diamonds</td>
                    <td>1</td>
                    <td><span class="amount">Rp 40.000</span></td>
                    <td><span class="pay-chip qris">QRIS</span></td>
                    <td><span class="status-chip success">success</span></td>
                    <td>2025-08-28 10:10</td>
                    <td>2025-08-28 10:15</td>
                    <td>
                      <button class="btn btn-ghost" title="Lihat">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button class="btn btn-ghost" title="Edit">
                        <i class="fa fa-pen"></i>
                      </button>
                      <button class="btn btn-ghost" title="More">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td><code class="mono">XYZ99001</code></td>
                    <td>
                      <div class="user-cell">
                        <div class="avatar">SN</div>
                        <div class="meta">
                          <div class="name">Sinta Nabila</div>
                          <div class="sub">sinta@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Free Fire — 140 Diamonds</td>
                    <td>1</td>
                    <td><span class="amount">Rp 27.000</span></td>
                    <td><span class="pay-chip ovo">OVO</span></td>
                    <td><span class="status-chip pending">pending</span></td>
                    <td>2025-08-28 09:30</td>
                    <td>2025-08-28 09:41</td>
                    <td>
                      <button class="btn btn-ghost" title="Lihat">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button class="btn btn-ghost" title="Edit">
                        <i class="fa fa-pen"></i>
                      </button>
                      <button class="btn btn-ghost" title="More">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td><code class="mono">GP778899</code></td>
                    <td>
                      <div class="user-cell">
                        <div class="avatar">AL</div>
                        <div class="meta">
                          <div class="name">Andi Lazuardi</div>
                          <div class="sub">andi@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Genshin Impact — 300 Crystals</td>
                    <td>1</td>
                    <td><span class="amount">Rp 75.000</span></td>
                    <td><span class="pay-chip gopay">GoPay</span></td>
                    <td>
                      <span class="status-chip processed">processed</span>
                    </td>
                    <td>2025-08-27 17:55</td>
                    <td>2025-08-27 18:03</td>
                    <td>
                      <button class="btn btn-ghost" title="Lihat">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button class="btn btn-ghost" title="Edit">
                        <i class="fa fa-pen"></i>
                      </button>
                      <button class="btn btn-ghost" title="More">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td><code class="mono">VR112233</code></td>
                    <td>
                      <div class="user-cell">
                        <div class="avatar">RP</div>
                        <div class="meta">
                          <div class="name">Raka Pratama</div>
                          <div class="sub">raka@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Valorant — 700 VP</td>
                    <td>1</td>
                    <td><span class="amount">Rp 80.000</span></td>
                    <td><span class="pay-chip bcava">BCA VA</span></td>
                    <td><span class="status-chip failed">failed</span></td>
                    <td>2025-08-27 12:01</td>
                    <td>2025-08-27 12:10</td>
                    <td>
                      <button class="btn btn-ghost" title="Lihat">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button class="btn btn-ghost" title="Edit">
                        <i class="fa fa-pen"></i>
                      </button>
                      <button class="btn btn-ghost" title="More">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td><code class="mono">SW551122</code></td>
                    <td>
                      <div class="user-cell">
                        <div class="avatar">SN</div>
                        <div class="meta">
                          <div class="name">Sinta Nabila</div>
                          <div class="sub">sinta@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Steam Wallet (IDR) — IDR 120.000</td>
                    <td>1</td>
                    <td><span class="amount">Rp 120.000</span></td>
                    <td><span class="pay-chip dana">DANA</span></td>
                    <td><span class="status-chip expired">expired</span></td>
                    <td>2025-08-25 08:00</td>
                    <td>2025-08-25 08:02</td>
                    <td>
                      <button class="btn btn-ghost" title="Lihat">
                        <i class="fa fa-eye"></i>
                      </button>
                      <button class="btn btn-ghost" title="Edit">
                        <i class="fa fa-pen"></i>
                      </button>
                      <button class="btn btn-ghost" title="More">
                        <i class="fa fa-ellipsis-v"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="legend">
            <span class="status-chip pending">pending</span>
            <span class="status-chip paid">paid</span>
            <span class="status-chip processed">processed</span>
            <span class="status-chip success">success</span>
            <span class="status-chip failed">failed</span>
            <span class="status-chip expired">expired</span>
            <span class="status-chip cancelled">cancelled</span>
          </div>
        </div>
      </section>
    </main>
    <footer class="footer"></footer>
  </body>
</html>
