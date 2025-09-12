<?php 
include_once("inc/koneksi.php");
?>

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>VAZATECH — Topup Game</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Lexend+Tera:wght@100..900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./assets/css/user.css" />
  </head>
  <body>
    <header>
      <!-- Logo di kiri (SESUAI BASE KAMU) -->
      <div class="logo">
        <img src="./image/logo_nocapt.png" alt="Logo" />
        <span class="logo">V A Z A T E C H</span>
      </div>

      <!-- Search di tengah -->
      <form class="search" action="#" method="get" role="search">
        <input
          name="q"
          type="text"
          class="search-input"
          placeholder="Pencarian"
          aria-label="Pencarian"
        />
      </form>

      <!-- Aksi di kanan -->
      <nav class="actions">
        <a href="#" class="cart" aria-label="Keranjang">
          <!-- ikon cart -->
          <svg
            viewBox="0 0 24 24"
            width="26"
            height="26"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <circle cx="9" cy="20" r="1.5"></circle>
            <circle cx="18" cy="20" r="1.5"></circle>
            <path
              d="M1.5 1.5h3l2.4 12.5a2 2 0 0 0 2 1.6h9.9a2 2 0 0 0 2-1.6l1.6-8.5H6.2"
            ></path>
          </svg>
          <span class="badge" aria-hidden="true">0</span>
        </a>
        <a href="./login.php" class="btn btn-login">Masuk</a>
      </nav>
    </header>
    <div class="topbar-accent" aria-hidden="true"></div>