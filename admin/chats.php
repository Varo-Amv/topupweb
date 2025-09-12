<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VAZATECH Admin · Chats</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Lexend+Tera:wght@100..900&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/admin.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link rel="stylesheet" href="admin.css" />
  </head>
  <body>
    <header>
      <div class="logo">
        <img src="../image/logo_nocapt.png" alt="Logo" />
        <span class="logo">V A Z A T E C H</span>
      </div>
      <div class="profile">
        <a href="../#"
          ><img src="../image/profile_white.png" alt="Profile"
        /></a>
      </div>
    </header>

    <main class="container">
      <aside class="sidebar">
        <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
        <a href="#" class="active"><i class="fas fa-comments"></i>Chats</a>
        <a href="stocks.php"><i class="fas fa-box"></i>Stocks</a>
        <a href="users.php"><i class="fas fa-users"></i>Users</a>
        <a href="orders.php"><i class="fas fa-shopping-cart"></i>Orders</a>
      </aside>

      <div class="content">
        <div class="dashboard-header">
          <h2>Chats</h2>
          <div class="cards-row">
            <div class="card kpi">
              <a>Open</a>
              <div id="kpiOpen" class="kpi-value">0</div>
            </div>
            <div class="card kpi">
              <a>Pending</a>
              <div id="kpiPending" class="kpi-value">0</div>
            </div>
            <div class="card kpi">
              <a>Closed</a>
              <div id="kpiClosed" class="kpi-value">0</div>
            </div>
          </div>
        </div>

        <div class="chat-layout">
          <div class="chat-list-pane">
            <div class="chat-toolbar">
              <input
                id="q"
                type="text"
                placeholder="Cari nama/email/subject/order code"
              />
              <select id="statusSelect">
                <option value="all">All</option>
                <option value="open">Open</option>
                <option value="pending">Pending</option>
                <option value="closed">Closed</option>
              </select>
              <button id="newChatBtn"><i class="fas fa-plus"></i> New</button>
            </div>
            <div id="chatList" class="chat-list"></div>
          </div>

          <div class="chat-thread-pane">
            <div class="thread-header">
              <div id="threadTitle">Pilih chat</div>
              <div class="spacer"></div>
              <select id="threadStatus" disabled>
                <option value="open">Open</option>
                <option value="pending">Pending</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <div id="thread" class="messages">
              <div class="placeholder">Belum ada chat dipilih.</div>
            </div>
            <form class="composer" id="composer" onsubmit="return false;">
              <input type="hidden" id="chatId" />
              <textarea
                id="msgBox"
                placeholder="Tulis pesan..."
                disabled
              ></textarea>
              <button id="sendBtn" disabled>Kirim</button>
            </form>
          </div>
        </div>
      </div>
    </main>
    <footer></footer>
    <!-- di akhir chats.php -->
<script src="../assets/js/chats.js"></script>
    <script>
const API = '../assets/api/chats.php';

let currentChatId = null;
let pollTimer = null;

const els = {
  list:       document.getElementById('chatList'),
  messages:   document.getElementById('messages'),
  title:      document.getElementById('threadTitle'),
  statusSel:  document.getElementById('threadStatus'),
  q:          document.getElementById('q'),
  filter:     document.getElementById('filterStatus'),
  msgBox:     document.getElementById('msgBox'),
  btnSend:    document.getElementById('btnSend'),
  btnNew:     document.getElementById('btnNew'),
  kpiOpen:    document.getElementById('kpiOpen'),
  kpiPending: document.getElementById('kpiPending'),
  kpiClosed:  document.getElementById('kpiClosed'),
};

async function api(url, opt={}) {
  const r = await fetch(url, {headers:{'X-Requested-With':'fetch'}, ...opt});
  return r.json();
}

/* ========== KPI ========== */
async function loadStats(){
  try {
    const s = await api(`${API}?action=stats`);
    if (els.kpiOpen)    els.kpiOpen.textContent    = s.open ?? 0;
    if (els.kpiPending) els.kpiPending.textContent = s.pending ?? 0;
    if (els.kpiClosed)  els.kpiClosed.textContent  = s.closed ?? 0;
  } catch(_) {}
}

/* ========== LIST ========== */
async function loadList(){
  const q = encodeURIComponent(els.q.value.trim());
  const s = encodeURIComponent(els.filter.value);
  const rows = await api(`${API}?action=list&q=${q}&status=${s}`);
  els.list.innerHTML = rows.map(r => `
    <div class="item" data-id="${r.id}" onclick="openChat(${r.id})">
      <div class="subject">${escapeHtml(r.subject || '(tanpa subject)')}</div>
      <div class="meta">
        <span class="chip ${r.status}">${r.status}</span>
        <span class="time">${(r.last_time || r.created_at || '').replace('T',' ')}</span>
      </div>
      <div class="prev">${escapeHtml(r.last_message || '')}</div>
    </div>
  `).join('') || `<div class="placeholder">Tidak ada chat.</div>`;
}

/* ========== THREAD ========== */
async function openChat(id){
  currentChatId = id;
  clearInterval(pollTimer);

  // set judul
  const item = els.list.querySelector(`.item[data-id="${id}"]`);
  els.title.textContent = item ? item.querySelector('.subject').textContent : `Chat #${id}`;

  await loadMessages();
  // auto-polling tiap 5s
  pollTimer = setInterval(loadMessages, 5000);
}

async function loadMessages(){
  if (!currentChatId) return;
  const msgs = await api(`${API}?action=messages&chat_id=${currentChatId}`);
  els.messages.innerHTML = msgs.map(m => `
    <div class="msg ${m.sender}">
      ${linkify(escapeHtml(m.message))}
      <span class="when">${m.created_at}</span>
    </div>
  `).join('') || `<div class="placeholder">Belum ada pesan.</div>`;
  els.messages.scrollTop = els.messages.scrollHeight;
}

/* ========== SEND ========== */
async function send(){
  const text = els.msgBox.value.trim();
  if (!text || !currentChatId) return;
  els.msgBox.value = '';
  await api(API, {
    method:'POST',
    body:new URLSearchParams({
      action:'send',
      chat_id: currentChatId,
      sender: 'admin',
      message: text
    })
  });
  loadMessages();
}

/* ========== NEW CHAT ========== */
async function createChat(){
  const subject = prompt('Subject chat:','Bantuan');
  if (!subject) return;
  const res = await api(API, {
    method:'POST',
    body:new URLSearchParams({action:'create', user_id:0, subject})
  });
  await loadList();
  if (res.chat_id) openChat(res.chat_id);
}

/* ========== HELPERS ========== */
function escapeHtml(s){return s.replace(/[&<>"']/g,m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));}
function linkify(t){return t.replace(/(https?:\/\/[^\s]+)/g,'<a href="$1" target="_blank" rel="noopener">$1</a>');}

/* ========== EVENTS ========== */
els.q?.addEventListener('input', ()=>{ clearTimeout(els._q); els._q=setTimeout(loadList,300); });
els.filter?.addEventListener('change', loadList);
els.btnSend?.addEventListener('click', send);
els.msgBox?.addEventListener('keydown', e=>{ if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); send(); }});
els.btnNew?.addEventListener('click', createChat);
els.statusSel?.addEventListener('change', async e=>{
  if (!currentChatId) return;
  await api(API, {method:'POST', body:new URLSearchParams({action:'set_status', chat_id: currentChatId, status: e.target.value})});
  loadList(); loadStats();
});

/* ========== INIT ========== */
loadStats(); loadList();
setInterval(loadStats, 10000);
</script>
<script>
(function(){
  const API_SSE  = '../assets/api/chat_stats_sse.php';
  const API_POLL = '../assets/api/chats.php?action=stats';

  const elOpen    = document.getElementById('kpiOpen');
  const elPending = document.getElementById('kpiPending');
  const elClosed  = document.getElementById('kpiClosed');

  function render(s){
    if (!s) return;
    if (elOpen)    elOpen.textContent    = s.open ?? 0;
    if (elPending) elPending.textContent = s.pending ?? 0;
    if (elClosed)  elClosed.textContent  = s.closed ?? 0;
  }

  // fallback polling (kalau SSE gak bisa)
  let pollTimer = null;
  async function startPolling(){
    if (pollTimer) clearTimeout(pollTimer);
    async function tick(){
      try {
        const r = await fetch(API_POLL, {headers:{'X-Requested-With':'fetch'}});
        const s = await r.json();
        render(s);
      } catch(_) {}
      pollTimer = setTimeout(tick, 5000); // tiap 5 detik
    }
    tick();
  }

  // SSE realtime
  function startSSE(){
    if (!('EventSource' in window)) { startPolling(); return; }
    const es = new EventSource(API_SSE);

    es.addEventListener('stats', (e) => {
      try { render(JSON.parse(e.data)); } catch(_){}
    });

    es.onerror = () => { // kalau koneksi gagal, fallback ke polling
      try { es.close(); } catch(_){}
      startPolling();
    };

    window.addEventListener('beforeunload', () => { try { es.close(); } catch(_{}); });
  }

  // boot
  startSSE();
})();
</script>
<script>
  const CHAT_KPI_URL = '../assets/api/chat_kpi.php'; // sesuaikan path relatifnya
  const POLL_MS = 7000; // tiap 7 detik refresh

  const elOpen    = document.getElementById('kpiOpen');
  const elPending = document.getElementById('kpiPending');
  const elClosed  = document.getElementById('kpiClosed');

  async function loadChatKPI() {
    try {
      const r = await fetch(CHAT_KPI_URL, {cache: 'no-store'});
      const j = await r.json();
      elOpen.textContent    = (j.open    ?? 0).toLocaleString('id-ID');
      elPending.textContent = (j.pending ?? 0).toLocaleString('id-ID');
      elClosed.textContent  = (j.closed  ?? 0).toLocaleString('id-ID');
    } catch (e) {
      console.error('loadChatKPI:', e);
    }
  }

  loadChatKPI();
  setInterval(loadChatKPI, POLL_MS);
</script>

  </body>
</html>