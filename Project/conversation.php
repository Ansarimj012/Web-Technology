<?php
session_start();
include_once "api/config.php";

if (!isset($_SESSION['unique_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int) $_GET['user_id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE unique_id=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: users.php");
    exit();
}

$row = mysqli_fetch_assoc($result);
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Sora:wght@300;400;500;600;700&display=swap');
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#050B14;--panel:#0B1624;--card:#101D2E;
  --accent:#00C2FF;--blue:#2563EB;--green:#22C55E;
  --border:rgba(0,194,255,0.18);--text:#FFFFFF;
  --text2:#94A3B8;--text3:#64748B;--danger:#FF5F6D;
}
html,body{height:100%;overflow:hidden}
body{font-family:'Sora',sans-serif;background:var(--bg);color:var(--text)}
body::before{
  content:'';position:fixed;inset:0;
  background-image:linear-gradient(rgba(0,194,255,0.025) 1px,transparent 1px),
                   linear-gradient(90deg,rgba(0,194,255,0.025) 1px,transparent 1px);
  background-size:40px 40px;pointer-events:none;z-index:0
}

/* ── FULLSCREEN CHAT LAYOUT ── */
.chat-wrap{
  display:flex;flex-direction:column;
  height:100vh;width:100vw;
  position:fixed;inset:0;z-index:10
}

/* ── HEADER ── */
.chat-header{
  background:linear-gradient(90deg,#0a1929,#0f2540,#091523);
  padding:14px 24px;
  display:flex;align-items:center;gap:14px;
  border-bottom:1px solid var(--border);
  flex-shrink:0;position:relative;z-index:20
}
.chat-header::after{
  content:'';position:absolute;bottom:0;left:0;right:0;
  height:1px;background:linear-gradient(90deg,transparent,var(--accent),transparent);opacity:.5
}
.ch-back{
  width:38px;height:38px;border-radius:11px;
  background:rgba(0,194,255,0.07);border:1px solid var(--border);
  color:var(--text3);display:flex;align-items:center;justify-content:center;
  text-decoration:none;transition:all .2s;flex-shrink:0
}
.ch-back:hover{background:rgba(0,194,255,0.15);border-color:rgba(0,194,255,0.4);color:var(--accent)}
.hdr-avatar{
  width:44px;height:44px;border-radius:50%;object-fit:cover;
  border:2px solid rgba(0,194,255,0.35);
  box-shadow:0 0 14px rgba(0,194,255,0.2);flex-shrink:0
}
.hdr-info{flex:1;min-width:0}
.hdr-name{
  font-family:'Orbitron',monospace;font-size:15px;font-weight:900;
  color:var(--text);letter-spacing:.8px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis
}
.hdr-status{
  font-size:11px;color:var(--green);margin-top:3px;
  display:flex;align-items:center;gap:6px
}
.hdr-status::before{
  content:'';width:6px;height:6px;border-radius:50%;
  background:var(--green);box-shadow:0 0 7px var(--green);animation:pulse 2s infinite
}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.hdr-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.enc-pill{
  display:inline-flex;align-items:center;gap:5px;
  background:rgba(0,194,255,0.08);border:1px solid rgba(0,194,255,0.2);
  border-radius:999px;padding:5px 12px;
  font-size:10px;color:rgba(0,194,255,0.75);font-weight:700;
  font-family:'Orbitron',monospace;letter-spacing:.7px;white-space:nowrap
}
.lock-tog{
  background:rgba(0,194,255,0.07);border:1px solid var(--border);
  border-radius:10px;padding:8px 12px;color:var(--text3);font-size:11px;
  cursor:pointer;transition:all .2s;
  display:flex;align-items:center;gap:6px;
  font-family:'Orbitron',monospace;letter-spacing:.5px;text-decoration:none
}
.lock-tog:hover{color:var(--accent);border-color:rgba(0,194,255,0.35);background:rgba(0,194,255,0.1)}

/* ── CHAT BODY ── */
.chat-body{
  flex:1;overflow-y:auto;
  padding:24px 0;
  display:flex;flex-direction:column;
  scrollbar-width:thin;scrollbar-color:var(--border) transparent;
  background:var(--bg);position:relative
}
.chat-body::before{
  content:'';position:fixed;top:60px;left:50%;transform:translateX(-50%);
  width:800px;height:300px;
  background:radial-gradient(ellipse,rgba(0,194,255,0.04) 0%,transparent 70%);
  pointer-events:none
}
.chat-body::-webkit-scrollbar{width:4px}
.chat-body::-webkit-scrollbar-thumb{background:var(--border);border-radius:4px}

/* ── CENTERED INNER WRAPPER ── */
.chat-body-inner{
  width:100%;max-width:780px;
  margin:0 auto;
  padding:0 32px;
  display:flex;flex-direction:column;gap:6px;
  flex:1
}

/* ── DATE SEPARATOR ── */
.date-sep{display:flex;align-items:center;gap:12px;padding:10px 0;margin:12px 0}
.date-sep::before,.date-sep::after{
  content:'';flex:1;height:1px;
  background:linear-gradient(to right,transparent,var(--border))
}
.date-sep::after{background:linear-gradient(to left,transparent,var(--border))}
.date-sep span{
  font-family:'Orbitron',monospace;font-size:10px;color:var(--text3);
  letter-spacing:1.5px;white-space:nowrap;
  padding:4px 14px;background:var(--card);
  border:1px solid var(--border);border-radius:999px;
  box-shadow:0 0 12px rgba(0,194,255,0.06)
}

/* ── BUBBLES ── */
.chat-message{margin-bottom:4px}
.msg-out{display:flex;justify-content:flex-end;margin-left:auto}
.msg-out .bbl{
  background:linear-gradient(135deg,#0d6ea3,#00C2FF);
  color:#fff;border-radius:22px 22px 6px 22px;
  padding:12px 18px;max-width:68%;
  font-size:14px;line-height:1.6;word-break:break-word;
  box-shadow:0 4px 20px rgba(0,194,255,0.2)
}
.msg-in{display:flex;align-items:flex-end;gap:10px}
.sav{
  width:32px;height:32px;border-radius:50%;object-fit:cover;
  border:2px solid var(--border);flex-shrink:0
}
.msg-in .bbl{
  background:var(--card);color:var(--text);
  border-radius:22px 22px 22px 6px;
  padding:12px 18px;max-width:68%;
  font-size:14px;line-height:1.6;word-break:break-word;
  border:1px solid var(--border)
}
.msg-time{
  font-size:10px;color:var(--text3);margin-top:4px;
  text-align:right;padding-right:4px;
  font-family:'Orbitron',monospace;letter-spacing:.5px;
  display:flex;align-items:center;justify-content:flex-end;gap:6px
}
.msg-time-in{
  font-size:10px;color:var(--text3);margin-top:4px;padding-left:42px;
  font-family:'Orbitron',monospace;letter-spacing:.5px
}
.del-btn{
  background:none;border:none;cursor:pointer;padding:0 2px;
  opacity:.35;transition:opacity .2s;vertical-align:middle
}
.del-btn:hover{opacity:1}
.del-btn img{width:12px}

/* ── ENC BAR ── */
.enc-bar{
  text-align:center;padding:8px;
  font-size:10px;color:rgba(0,194,255,0.4);
  letter-spacing:1.5px;font-family:'Orbitron',monospace;
  flex-shrink:0;display:flex;align-items:center;justify-content:center;gap:8px
}

/* ── TYPING AREA ── */
.typing-wrap{
  background:linear-gradient(90deg,#0a1929,#091523);
  border-top:1px solid var(--border);
  padding:12px 0 16px;
  flex-shrink:0
}
.typing-inner{
  width:100%;max-width:780px;
  margin:0 auto;
  padding:0 32px
}
.typing-row{
  display:flex;align-items:center;gap:10px;
  background:rgba(0,194,255,0.08);
  border-radius:18px;
  padding:8px 10px 8px 18px
}
.typing-row textarea{
  flex:1;background:transparent;
  border:none !important;outline:none !important;box-shadow:none !important;
  color:var(--text);font-family:'Sora',sans-serif;font-size:15px;
  resize:none;overflow:hidden;scrollbar-width:none;
  height:28px;min-height:28px;max-height:120px;
  line-height:28px;padding:0
}
.typing-row textarea::-webkit-scrollbar{display:none}
.typing-row textarea:focus,
.typing-row:focus-within{border:none !important;outline:none !important;box-shadow:none !important}
.typing-row textarea::placeholder{color:var(--text3);font-size:15px;line-height:28px}
.send-btn{
  width:42px;height:42px;border-radius:13px;
  background:linear-gradient(135deg,var(--accent),var(--blue));
  border:none;color:#fff;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;transition:all .2s;
  box-shadow:0 0 20px rgba(0,194,255,0.35)
}
.send-btn:hover{transform:scale(1.08) rotate(5deg);box-shadow:0 0 32px rgba(0,194,255,0.55)}
.send-btn:active{transform:scale(0.94)}

/* ── DELETE POPUP ── */
#chatDeletePopup{
  display:flex;
  justify-content:center;
  margin:12px 0;
  animation:fadePop .2s ease
}
.mini-delete-box{
  background:#07111f;
  border:1px solid rgba(0,194,255,.18);
  border-radius:14px;
  padding:14px;
  min-width:240px;
  text-align:center;
  box-shadow:0 12px 30px rgba(0,0,0,.35)
}
.mini-delete-text{
  color:#fff;
  font-size:14px;
  margin-bottom:12px
}
.mini-delete-actions{
  display:flex;
  gap:10px;
  justify-content:center
}
.mini-cancel-btn,
.mini-delete-btn{
  border:none;
  padding:8px 14px;
  border-radius:10px;
  cursor:pointer;
  font-size:13px;
  font-weight:600
}
.mini-cancel-btn{background:#1e293b;color:#fff}
.mini-delete-btn{background:#ff3b30;color:#fff}
.mini-cancel-btn:hover{background:#334155}
.mini-delete-btn:hover{background:#e11d48}
@keyframes fadePop{
  from{opacity:0;transform:translateY(10px)}
  to{opacity:1;transform:translateY(0)}
}

/* ── RESPONSIVE ── */
@media(max-width:600px){
  .typing-inner,.chat-body-inner{padding:0 18px}
  .chat-header{padding:12px 16px}
  .hdr-actions .enc-pill{display:none}
}
</style>

<!-- FULLSCREEN CHAT UI -->
<div class="chat-wrap" id="chat-ui">

  <!-- Header -->
  <div class="chat-header">
    <a href="users.php" class="ch-back" title="Back to chats">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <img class="hdr-avatar"
         src="api/images/pfp/<?php echo htmlspecialchars($row['img']); ?>"
         alt=""
         onerror="this.src='api/images/pfp/logo.jpg'">
    <div class="hdr-info">
      <div class="hdr-name"><?php echo htmlspecialchars($row['username']); ?></div>
      <div class="hdr-status"><?php echo htmlspecialchars($row['status'] ?: 'Active'); ?></div>
    </div>
    <div class="hdr-actions">
      <a href="profile.php?user_id=<?php echo $user_id; ?>" class="lock-tog" title="View Profile">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        PROFILE
      </a>
      <div class="enc-pill">
        <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 1l9 4v6c0 5.25-3.75 10.15-9 11.5C6.75 21.15 3 16.25 3 11V5l9-4z"/>
        </svg>
        ENCRYPTED
      </div>
    </div>
  </div>

  <!-- Messages -->
  <div class="chat-body" id="chat-box">
    <div class="chat-body-inner" id="chat-inner"></div>
  </div>

  <!-- Enc bar -->
  <div class="enc-bar">
    <svg width="10" height="10" fill="rgba(0,194,255,0.5)" viewBox="0 0 24 24">
      <path d="M12 1l9 4v6c0 5.25-3.75 10.15-9 11.5C6.75 21.15 3 16.25 3 11V5l9-4z"/>
    </svg>
    AES-256 END-TO-END ENCRYPTED CHANNEL
  </div>

  <!-- Typing -->
  <div class="typing-wrap">
    <div class="typing-inner">
      <form class="typing-row" id="chat-form">
        <input type="hidden" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>">
        <textarea
          name="message"
          id="chat-msg"
          rows="1"
          placeholder="Send an encrypted message…"
          autocomplete="off"
          oninput="autoResize(this)"></textarea>
        <button type="submit" class="send-btn" title="Send">
          <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
        </button>
      </form>
    </div>
  </div>

</div>

<script>
const INCOMING_ID = <?php echo $user_id; ?>;

const form      = document.getElementById('chat-form');
const msgField  = document.getElementById('chat-msg');
const chatBox   = document.getElementById('chat-box');
const chatInner = document.getElementById('chat-inner');

/* Auto-resize textarea */
function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

/* Send message */
form.addEventListener('submit', e => {
  e.preventDefault();
  if (!msgField.value.trim()) return;
  fetch('api/send-message.php', {
    method: 'POST',
    body: new FormData(form)
  })
  .then(res => res.text())
  .then(() => {
    msgField.value = '';
    msgField.style.height = '28px';
    fetchMsgs();
  })
  .catch(err => console.log(err));
});

/* Send on Enter (Shift+Enter = new line) */
msgField.addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    form.dispatchEvent(new Event('submit'));
  }
});

/* Check scroll position */
function isNearBottom() {
  return chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 80;
}

/* Fetch and render messages */
function fetchMsgs() {
  const atBot = isNearBottom();
  fetch('api/load-messages.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'incoming_id=' + INCOMING_ID
  })
  .then(res => res.text())
  .then(html => {
    chatInner.innerHTML = html;
    if (atBot) chatBox.scrollTop = chatBox.scrollHeight;
  })
  .catch(err => console.log(err));
}

/* Delete message popup */
function delete_msg_fun(msg_id) {
  let oldPopup = document.getElementById('chatDeletePopup');
  if (oldPopup) oldPopup.remove();
  clearInterval(window.fetchInterval);

  let popup = document.createElement('div');
  popup.id = 'chatDeletePopup';
  popup.innerHTML = `
    <div class="mini-delete-box">
      <div class="mini-delete-text">Delete this message?</div>
      <div class="mini-delete-actions">
        <button class="mini-cancel-btn">Cancel</button>
        <button class="mini-delete-btn">Delete</button>
      </div>
    </div>
  `;

  chatInner.appendChild(popup);
  chatBox.scrollTop = chatBox.scrollHeight;

  popup.querySelector('.mini-cancel-btn').onclick = () => {
    popup.remove();
    window.fetchInterval = setInterval(fetchMsgs, 10000);
  };

  popup.querySelector('.mini-delete-btn').onclick = () => {
    fetch('api/delete_msg.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'msg_id=' + msg_id
    })
    .then(res => res.text())
    .then(() => {
      popup.remove();
      fetchMsgs();
      window.fetchInterval = setInterval(fetchMsgs, 10000);
    })
    .catch(err => console.log(err));
  };
}

/* Start on load */
document.addEventListener('DOMContentLoaded', () => {
  fetchMsgs();
  window.fetchInterval = setInterval(fetchMsgs, 10000);
  msgField.focus();
});
</script>

</body>
</html>