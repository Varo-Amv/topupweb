<?php
require_once __DIR__ . '/../inc/db.php';
session_start();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>VAZATECH · Admin · Chats</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="chats.css">
</head>
<body>
  <div class="layout">
    <aside class="sidebar">
      <div class="brand">VAZATECH</div>
      <nav>
        <a href="index.php">🏠 Dashboard</a>
        <a class="active" href="chats.php">💬 Chats</a>
        <a href="stocks.php">📦 Stocks</a>
        <a href="users.php">👥 Users</a>
        <a href="orders.php">🛒 Orders</a>
      </nav>
    </aside>
    <main class="content">
      <header class="toolbar">
        <div class="search">
          <input id="q" type="text" placeholder="Search chats (name, email, subject, order code)">
        </div>
        <div class="filters">
          <select id="statusFilter">
            <option value="">All</option>
            <option value="open">Open</option>
            <option value="pending">Pending</option>
            <option value="closed">Closed</option>
          </select>
          <button id="newChatBtn">+ New Chat</button>
        </div>
      </header>
      <section class="split">
        <div class="list" id="chatList"></div>
        <div class="thread">
          <div class="thread-header">
            <div id="threadTitle">Select a chat</div>
            <div class="spacer"></div>
            <select id="threadStatus" disabled>
              <option value="open">Open</option>
              <option value="pending">Pending</option>
              <option value="closed">Closed</option>
            </select>
          </div>
          <div class="messages" id="messages">
            <div class="placeholder">No chat selected.</div>
          </div>
          <form class="composer" id="composer" onsubmit="return false;">
            <input type="hidden" id="chatId">
            <textarea id="messageInput" placeholder="Type a message..." disabled></textarea>
            <button id="sendBtn" disabled>Send</button>
          </form>
        </div>
      </section>
    </main>
  </div>
  <script src="chats.js"></script>
</body>
</html>
