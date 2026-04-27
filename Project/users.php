<?php
session_start();
$_NAV['current_pos'] = 'user';
include_once "api/config.php";
if (!isset($_SESSION['unique_id'])) { header("location: auth/login.php"); exit(); }
$uid = $_SESSION['unique_id'];
$user_sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$uid}");
$current_user = mysqli_fetch_assoc($user_sql);
?>
<?php include_once "header.php"; ?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Sora:wght@300;400;500;600;700&display=swap');
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#050B14;--panel:#0B1624;--card:#101D2E;
  --accent:#00C2FF;--cyan:#00E5FF;--blue:#2563EB;
  --green:#22C55E;--border:rgba(0,194,255,0.18);
  --hover:rgba(0,194,255,0.04);--danger:#FF5F6D;
  --text:#FFFFFF;--text2:#94A3B8;--text3:#64748B;
}
body{font-family:'Sora',sans-serif;background:var(--bg);color:var(--text);height:100vh;overflow:hidden}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(0,194,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,194,255,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;z-index:0}

.app-shell{display:flex;height:100vh;overflow:hidden;position:relative;z-index:1}

/* ── SIDEBAR ── */
.sidebar{width:360px;min-width:300px;background:var(--panel);border-right:1px solid var(--border);display:flex;flex-direction:column;height:100vh;position:relative;flex-shrink:0}
.sidebar::after{content:'';position:absolute;top:0;right:0;width:1px;height:100%;background:linear-gradient(to bottom,transparent,var(--accent),transparent);opacity:.4;pointer-events:none}

/* Header */
.sb-header{background:linear-gradient(90deg,#0F1F35,#13263D,#0C1828);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);flex-shrink:0;position:relative}
.sb-header::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--accent),transparent);opacity:.5}
.sb-title{font-family:'Orbitron',monospace;font-size:18px;font-weight:700;color:var(--text);letter-spacing:2px;text-transform:uppercase}
.sb-title span{color:var(--accent)}

.logout-btn{display:flex;align-items:center;gap:7px;padding:7px 14px;background:rgba(255,95,109,0.08);border:1px solid rgba(255,95,109,0.25);border-radius:10px;color:var(--danger);font-size:11px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;font-family:'Orbitron',monospace;letter-spacing:.5px}
.logout-btn:hover{background:rgba(255,95,109,0.18);border-color:rgba(255,95,109,0.5);box-shadow:0 0 14px rgba(255,95,109,0.2);transform:translateY(-1px)}

/* Search */
.search-wrap{padding:12px 14px;background:var(--panel);border-bottom:1px solid var(--border);flex-shrink:0}
.search-field{position:relative;display:flex;align-items:center}
.search-field .s-icon{position:absolute;left:13px;color:var(--text3);pointer-events:none;transition:color .25s}
.search-field input{width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:12px;padding:10px 40px 10px 40px;color:var(--text);font-family:'Sora',sans-serif;font-size:13px;outline:none;transition:all .25s}
.search-field input::placeholder{color:var(--text3)}
.search-field input:focus{border-color:rgba(0,194,255,0.5);background:#0d1a28;box-shadow:0 0 0 3px rgba(0,194,255,0.08)}
.search-field:focus-within .s-icon{color:var(--accent)}
.s-clear{position:absolute;right:12px;background:none;border:none;color:var(--text3);cursor:pointer;font-size:14px;display:none;padding:0;transition:color .2s}
.s-clear:hover{color:var(--danger)}

/* Search results */
#srp{flex-shrink:0;overflow-y:auto;max-height:0;transition:max-height .3s ease;background:var(--card);border-bottom:1px solid var(--border)}
#srp.open{max-height:300px}
#srp::-webkit-scrollbar{width:3px}
#srp::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
.sri{display:flex;align-items:center;gap:12px;padding:11px 16px;border-bottom:1px solid rgba(0,194,255,0.06);cursor:pointer;transition:background .15s;animation:fadeUp .18s ease}
.sri:last-child{border-bottom:none}
.sri:hover{background:var(--hover)}
.sri .av{width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0;transition:border-color .2s}
.sri:hover .av{border-color:rgba(0,194,255,0.4)}
.sri-info{flex:1;min-width:0}
.sri-name{font-weight:600;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sri-tag{font-size:11px;color:var(--text3);margin-top:1px}
.rel-badge{flex-shrink:0;border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;font-family:'Orbitron',monospace;border:none;cursor:pointer;text-decoration:none;transition:all .2s;letter-spacing:.5px}
.rel-badge.friend{background:rgba(34,197,94,0.1);color:var(--green);border:1px solid rgba(34,197,94,0.25)}
.rel-badge.pending{background:rgba(245,158,11,0.1);color:#F59E0B;border:1px solid rgba(245,158,11,0.25);cursor:default}
.rel-badge.add{background:linear-gradient(135deg,var(--accent),var(--blue));color:#fff;box-shadow:0 0 12px rgba(0,194,255,0.3)}
.rel-badge.add:hover{opacity:.85;box-shadow:0 0 20px rgba(0,194,255,0.45)}
#srp-empty{padding:24px;text-align:center;color:var(--text3);font-size:12px;display:none}
#srp-loader{padding:24px;text-align:center;color:var(--text3);font-size:12px;display:none}
.spin{width:16px;height:16px;border:2px solid rgba(0,194,255,0.2);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite;display:inline-block;vertical-align:middle;margin-right:6px}

/* Contacts row */
.contacts-row{padding:10px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);flex-shrink:0}
.contacts-label{font-family:'Orbitron',monospace;font-size:10px;font-weight:700;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase}

/* Contacts icon button - improved */
.contacts-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,rgba(0,194,255,0.12),rgba(37,99,235,0.12));border:1px solid rgba(0,194,255,0.3);border-radius:10px;padding:7px 14px;color:var(--accent);font-family:'Orbitron',monospace;font-size:10px;font-weight:700;letter-spacing:1px;cursor:pointer;text-decoration:none;transition:all .25s;text-transform:uppercase;position:relative}
.contacts-btn:hover{background:linear-gradient(135deg,rgba(0,194,255,0.22),rgba(37,99,235,0.22));border-color:rgba(0,194,255,0.6);box-shadow:0 0 16px rgba(0,194,255,0.25),0 4px 12px rgba(0,0,0,0.3);transform:translateY(-1px)}
.contacts-btn svg{color:var(--accent);transition:transform .2s}
.contacts-btn:hover svg{transform:rotate(15deg)}
.contacts-btn .notif-cnt{position:absolute;top:-5px;right:-5px;background:var(--danger);color:#fff;border-radius:999px;font-size:9px;font-weight:900;padding:2px 5px;min-width:16px;text-align:center;border:2px solid var(--panel);box-shadow:0 0 8px rgba(255,95,109,0.5)}

/* Chat list */
.chat-list{flex:1;overflow-y:auto;scrollbar-width:thin;scrollbar-color:var(--border) transparent}
.chat-list::-webkit-scrollbar{width:3px}
.chat-list::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}

.chat-item{display:flex;align-items:center;gap:13px;padding:12px 16px;border-bottom:1px solid rgba(0,194,255,0.05);cursor:pointer;transition:all .18s;position:relative}
.chat-item:hover{background:var(--hover)}
.chat-item.active{background:rgba(0,194,255,0.07)}
.chat-item.active::before{content:'';position:absolute;left:0;top:8px;bottom:8px;width:3px;background:linear-gradient(to bottom,var(--accent),var(--blue));border-radius:0 3px 3px 0;box-shadow:0 0 8px var(--accent)}
.chat-item .av{width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0;transition:border-color .2s}
.chat-item:hover .av,.chat-item.active .av{border-color:rgba(0,194,255,0.45)}
.ci-meta{flex:1;min-width:0}
.ci-name{font-size:14px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ci-preview{font-size:12px;color:var(--text3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}

/* Chat item action on hover */
.ci-actions{display:none;gap:6px;flex-shrink:0}
.chat-item:hover .ci-actions{display:flex}
.ci-msg-btn{background:linear-gradient(135deg,rgba(0,194,255,0.15),rgba(37,99,235,0.15));border:1px solid rgba(0,194,255,0.3);border-radius:8px;padding:5px 10px;color:var(--accent);font-size:10px;font-weight:700;font-family:'Orbitron',monospace;cursor:pointer;text-decoration:none;letter-spacing:.5px;transition:all .2s;white-space:nowrap}
.ci-msg-btn:hover{background:rgba(0,194,255,0.25);box-shadow:0 0 10px rgba(0,194,255,0.2)}

.empty-chats{padding:40px 16px;text-align:center;color:var(--text3)}
.empty-chats svg{margin:0 auto 14px;display:block;opacity:.25}
.empty-chats p{font-size:13px;line-height:1.7;color:var(--text3)}
.empty-hint{margin-top:14px;display:inline-flex;align-items:center;gap:6px;font-size:11px;color:var(--accent);font-weight:600;cursor:pointer;letter-spacing:.5px;opacity:.8}
.empty-hint:hover{opacity:1}

/* Profile bar */
.profile-bar-wrap{background:linear-gradient(90deg,#0F1F35,#0C1828);position:absolute;bottom:0;left:0;right:0;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px;padding:10px 14px;flex-shrink:0}
.profile-bar{display:flex;align-items:center;gap:10px;flex:1;cursor:pointer;border-radius:12px;padding:6px 10px;transition:background .2s}
.profile-bar:hover{background:rgba(0,194,255,0.05)}
.profile-bar .av{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid rgba(0,194,255,0.35);flex-shrink:0;box-shadow:0 0 8px rgba(0,194,255,0.15)}
.pb-info{flex:1;min-width:0}
.pb-name{font-family:'Orbitron',monospace;font-size:12px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:.5px}
.pb-online{font-size:11px;color:var(--green);margin-top:1px;display:flex;align-items:center;gap:5px}
.pb-online::before{content:'';width:5px;height:5px;border-radius:50%;background:var(--green);box-shadow:0 0 5px var(--green)}
.profile-btn{display:flex;align-items:center;gap:7px;padding:8px 12px;background:rgba(0,194,255,0.08);border:1px solid rgba(0,194,255,0.22);border-radius:10px;color:var(--accent);font-size:11px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;font-family:'Orbitron',monospace;letter-spacing:.5px;flex-shrink:0}
.profile-btn:hover{background:rgba(0,194,255,0.18);border-color:rgba(0,194,255,0.5);box-shadow:0 0 12px rgba(0,194,255,0.2);transform:translateY(-1px)}

/* Right panel */
.right-panel{flex:1;background:var(--bg);position:relative;overflow:hidden;display:flex;flex-direction:column}
.rp-glow1{position:absolute;top:-100px;left:50%;transform:translateX(-50%);width:700px;height:400px;background:radial-gradient(ellipse,rgba(0,194,255,0.05) 0%,transparent 70%);pointer-events:none}

/* Welcome */
.welcome{display:flex;align-items:center;justify-content:center;flex:1;position:relative;z-index:1}
.welcome.hidden{display:none}
.wc{text-align:center}
.wc-ring{width:90px;height:90px;border-radius:50%;border:1px solid rgba(0,194,255,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;position:relative;animation:pulseRing 3s ease-in-out infinite}
.wc-ring::before{content:'';position:absolute;inset:8px;border-radius:50%;border:1px solid rgba(0,194,255,0.12)}
.wc-ring svg{color:var(--accent);opacity:.7}
.wc-title{font-family:'Orbitron',monospace;font-size:24px;font-weight:900;color:var(--text);margin-bottom:10px;letter-spacing:3px}
.wc-sub{font-size:13px;color:var(--text3);line-height:1.8;max-width:260px;margin:0 auto}
.wc-enc{display:inline-flex;align-items:center;gap:6px;margin-top:20px;background:rgba(0,194,255,0.07);border:1px solid rgba(0,194,255,0.18);border-radius:999px;padding:5px 14px;font-size:11px;color:rgba(0,194,255,0.7);font-weight:700;letter-spacing:1px;font-family:'Orbitron',monospace}

/* Conversation */
.conv{display:none;flex-direction:column;height:100%;z-index:1;position:relative}
.conv.show{display:flex}
.conv-hdr{background:linear-gradient(90deg,#0F1F35,#13263D,#0C1828);padding:13px 20px;display:flex;align-items:center;gap:13px;border-bottom:1px solid var(--border);flex-shrink:0;position:relative}
.conv-hdr::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--accent),transparent);opacity:.4}
.conv-back{background:none;border:none;color:var(--text3);cursor:pointer;padding:0;display:flex;align-items:center;transition:color .2s}
.conv-back:hover{color:var(--accent)}
#conv-avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid rgba(0,194,255,0.3);box-shadow:0 0 10px rgba(0,194,255,0.15)}
.ch-name{font-family:'Orbitron',monospace;font-weight:700;font-size:14px;color:var(--text);letter-spacing:.5px}
.ch-status{font-size:11px;color:var(--green);margin-top:2px}
.enc-pill{margin-left:auto;display:inline-flex;align-items:center;gap:5px;background:rgba(0,194,255,0.08);border:1px solid rgba(0,194,255,0.2);border-radius:999px;padding:4px 10px;font-size:10px;color:rgba(0,194,255,0.7);font-weight:700;font-family:'Orbitron',monospace;letter-spacing:.5px;white-space:nowrap}

/* Open full chat button in header */
.open-full-btn{display:flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(0,194,255,0.08);border:1px solid rgba(0,194,255,0.25);border-radius:9px;color:var(--accent);font-size:10px;font-weight:700;font-family:'Orbitron',monospace;cursor:pointer;text-decoration:none;letter-spacing:.5px;transition:all .2s;white-space:nowrap;flex-shrink:0}
.open-full-btn:hover{background:rgba(0,194,255,0.18);border-color:rgba(0,194,255,0.5);box-shadow:0 0 10px rgba(0,194,255,0.2)}

/* Messages */
.conv-msgs{flex:1;overflow-y:auto;padding:16px 20px;display:flex;flex-direction:column;gap:4px;scrollbar-width:thin;scrollbar-color:var(--border) transparent;background:var(--bg)}
.conv-msgs::-webkit-scrollbar{width:3px}
.conv-msgs::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
.date-sep{display:flex;align-items:center;gap:10px;padding:8px 0;margin:8px 0}
.date-sep::before,.date-sep::after{content:'';flex:1;height:1px;background:linear-gradient(to right,transparent,var(--border))}
.date-sep::after{background:linear-gradient(to left,transparent,var(--border))}
.date-sep span{font-family:'Orbitron',monospace;font-size:10px;color:var(--text3);letter-spacing:1px;white-space:nowrap;padding:3px 10px;background:var(--card);border:1px solid var(--border);border-radius:999px}
.msg-out{display:flex;justify-content:flex-end;margin-bottom:2px}
.msg-out .bbl{background:linear-gradient(135deg,#0d6ea3,var(--accent));color:#fff;border-radius:18px 18px 4px 18px;padding:10px 15px;max-width:68%;font-size:13px;line-height:1.6;word-break:break-word;box-shadow:0 4px 16px rgba(0,194,255,0.2)}
.msg-in{display:flex;align-items:flex-end;gap:8px;margin-bottom:2px}
.msg-in .sav{width:26px;height:26px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0}
.msg-in .bbl{background:var(--card);color:var(--text);border-radius:18px 18px 18px 4px;padding:10px 15px;max-width:68%;font-size:13px;line-height:1.6;word-break:break-word;border:1px solid var(--border)}
.msg-time{font-size:10px;color:var(--text3);margin-top:3px;text-align:right;padding-right:2px}
.msg-time-in{font-size:10px;color:var(--text3);margin-top:3px;padding-left:34px}
.del-btn{background:none;border:none;cursor:pointer;padding:0 4px;opacity:.4;transition:opacity .2s;vertical-align:middle}
.del-btn:hover{opacity:1}
.del-btn img{width:11px}

/* Not-friend overlay */
.locked{flex:1;display:flex;align-items:center;justify-content:center;background:var(--bg)}
.lock-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:36px 28px;text-align:center;max-width:300px}
.lock-card svg{color:rgba(0,194,255,0.4);margin:0 auto 16px;display:block}
.lock-card h3{font-family:'Orbitron',monospace;font-size:16px;font-weight:700;color:var(--text);margin-bottom:8px;letter-spacing:1px}
.lock-card p{font-size:12px;color:var(--text3);line-height:1.7}
.lock-card a{display:inline-block;margin-top:18px;background:linear-gradient(135deg,var(--accent),var(--blue));color:#fff;border-radius:12px;padding:10px 22px;font-family:'Orbitron',monospace;font-size:11px;font-weight:700;text-decoration:none;letter-spacing:.5px;transition:opacity .2s}
.lock-card a:hover{opacity:.85}

/* Typing area */
/* Input Container */
.conv-inp{
    background:linear-gradient(90deg,#0F1F35,#0C1828);
    border-top:none;
    padding:12px 16px;
    flex-shrink:0;
}
/* Main input box */
.typing-row{
    display:flex;
    align-items:flex-end;
    gap:10px;
    background:rgba(0,194,255,0.08);
    border:none;
    outline:none;
    box-shadow:none;
    border-radius:16px;
    padding:10px 10px 10px 15px;
    position:relative;
}

/* Remove blue border from textarea */
.typing-row textarea{
    flex:1;
    background:transparent;
    border:none !important;
    outline:none !important;
    box-shadow:none !important;
    color:var(--text);
    font-family:'Sora',sans-serif;
    font-size:18px;
    resize:none;
    line-height:1.6;
    padding:4px 0;
}

/* Remove border when clicked */
.typing-row textarea:focus{
    border:none !important;
    outline:none !important;
    box-shadow:none !important;
}
/* No border on focus */
.typing-row:focus-within{
    box-shadow:none;
    border:none;
    outline:none;
}



/* Placeholder */
.typing-row textarea::placeholder{
    color:var(--text4);
    font-size:18px;          /* increased placeholder size */
}
.send-btn{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--blue));border:none;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;box-shadow:0 0 16px rgba(0,194,255,0.3)}
.send-btn:hover{opacity:.88;transform:scale(1.06);box-shadow:0 0 26px rgba(0,194,255,0.5)}

@keyframes fadeUp{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
@keyframes pulseRing{0%,100%{box-shadow:0 0 0 0 rgba(0,194,255,0.08)}50%{box-shadow:0 0 0 14px transparent}}
@keyframes spin{to{transform:rotate(360deg)}}

    
    /*for mobile responsive */
    /* ===============================
   MOBILE RESPONSIVE DESIGN
================================= */

/* Tablet */
    
    @media (max-width:768px){

body{
overflow:hidden;
}

.app-shell{
position:relative;
height:100vh;
}

/* sidebar full screen */
.sidebar{
position:absolute;
top:0;
left:0;
width:100%;
height:100%;
z-index:30;
transition:0.3s;
}

/* hide sidebar when chat opens */
.sidebar.hide{
left:-100%;
}

/* show right panel */
.right-panel{
display:flex !important;
width:100%;
height:100%;
}

/* chat screen */
.conv{
height:100%;
}

/* smaller header */
.conv-hdr{
padding:10px;
}

/* hide extra buttons */
.open-full-btn,
.enc-pill{
display:none;
}

/* messages */
.conv-msgs{
padding:10px;
}

.msg-out .bbl,
.msg-in .bbl{
max-width:88%;
font-size:13px;
}

/* input */
.conv-inp{
padding:8px;
}

.typing-row textarea{
font-size:15px;
}

.send-btn{
width:38px;
height:38px;
}

}
    
</style>

<div class="app-shell">

<!-- SIDEBAR -->
<div class="sidebar">

  <div class="sb-header">
    <div class="sb-title">Eny<span>CHAT</span></div>
    <a href="api/logout.php" class="logout-btn" title="Logout">
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      LOGOUT
    </a>
  </div>

  <!-- Search -->
  <div class="search-wrap">
    <div class="search-field">
      <svg class="s-icon" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/></svg>
      <input type="text" id="s-input" placeholder="Search users…" autocomplete="off">
      <button class="s-clear" id="s-clear">✕</button>
    </div>
  </div>

  <!-- Search Results -->
  <div id="srp">
    <div id="srp-loader"><span class="spin"></span>Searching…</div>
    <div id="srp-empty">No users found</div>
  </div>

  <!-- Contacts row -->
  <div class="contacts-row">
    <span class="contacts-label">Conversations</span>
    <?php
    $req_pending = mysqli_num_rows(mysqli_query($conn, "SELECT req_id FROM friend_req WHERE to_req_id = {$uid}"));
    ?>
    <a href="friends.php" class="contacts-btn" title="Contacts & Requests">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      CONTACTS
      <?php if($req_pending > 0): ?>
        <span class="notif-cnt"><?php echo $req_pending; ?></span>
      <?php endif; ?>
    </a>
  </div>

  <!-- Chat List -->
  <div class="chat-list" id="chat-list">
    <?php
    $sql = "SELECT * FROM friends LEFT JOIN users ON users.unique_id = friends.unique_id WHERE friend_id={$uid} ORDER BY last_msg_id DESC";
    $q = mysqli_query($conn, $sql);
    if (mysqli_num_rows($q) > 0) {
      while ($row = mysqli_fetch_assoc($q)) {
        $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id={$row['unique_id']} OR outgoing_msg_id={$row['unique_id']}) AND (outgoing_msg_id={$uid} OR incoming_msg_id={$uid}) ORDER BY msg_id DESC LIMIT 1";
        $q2 = mysqli_query($conn, $sql2);
        $r2 = mysqli_fetch_assoc($q2);
        $raw = isset($r2['msg']) ? decrypt_message($r2['msg']) : 'No messages yet';
        $preview = mb_strlen($raw) > 36 ? mb_substr($raw,0,36).'…' : $raw;
        echo '<div class="chat-item" data-uid="'.$row['unique_id'].'" data-name="'.htmlspecialchars($row['username']).'" data-img="'.htmlspecialchars($row['img']).'" data-status="'.htmlspecialchars($row['status']).'" data-rel="friend" onclick="openConv(this)">
          <img class="av" src="api/images/pfp/'.htmlspecialchars($row['img']).'" alt="" onerror="this.src=\'api/images/pfp/logo.jpg\'">
          <div class="ci-meta">
            <div class="ci-name">'.htmlspecialchars($row['username']).'</div>
            <div class="ci-preview">'.htmlspecialchars($preview).'</div>
          </div>
          <div class="ci-actions">
            <a href="conversation.php?user_id='.$row['unique_id'].'" class="ci-msg-btn" onclick="event.stopPropagation()">💬 CHAT</a>
          </div>
        </div>';
      }
    } else {
      echo '<div class="empty-chats">
        <svg width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <p>No conversations yet.<br>Add friends to start chatting!</p>
        <span class="empty-hint" onclick="location.href=\'friends.php\'">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          FIND PEOPLE
        </span>
      </div>';
    }
    ?>
  </div>

  <!-- Profile bottom bar -->
  <div class="profile-bar-wrap">
    <div class="profile-bar" onclick="location.href='myprofile.php'">
      <img class="av" src="api/images/pfp/<?php echo htmlspecialchars($current_user['img']); ?>" alt="" onerror="this.src='api/images/pfp/logo.jpg'">
      <div class="pb-info">
        <div class="pb-name"><?php echo htmlspecialchars($current_user['username']); ?></div>
        <div class="pb-online">Online</div>
      </div>
    </div>
    <a href="myprofile.php" class="profile-btn" title="My Profile">
      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      PROFILE
    </a>
  </div>

</div><!-- /sidebar -->

<!-- RIGHT PANEL -->
<div class="right-panel">
  <div class="rp-glow1"></div>

  <!-- Welcome -->
  <div class="welcome" id="welcome">
    <div class="wc">
      <div class="wc-ring">
        <svg width="34" height="34" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
      </div>
      <div class="wc-title">SECURE CHAT</div>
      <div class="wc-sub">Select a conversation to begin.<br>All messages are end-to-end encrypted.</div>
      <div class="wc-enc">
        <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1l9 4v6c0 5.25-3.75 10.15-9 11.5C6.75 21.15 3 16.25 3 11V5l9-4z"/></svg>
        AES-256 ENCRYPTED
      </div>
    </div>
  </div>

  <!-- Conversation panel -->
  <div class="conv" id="conv">
    <div class="conv-hdr">
      <button class="conv-back" onclick="closeConv()">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <img id="conv-avatar" src="" alt="" onerror="this.src='api/images/pfp/logo.jpg'">
      <div>
        <div class="ch-name" id="conv-name"></div>
        <div class="ch-status" id="conv-status"></div>
      </div>
      <!-- Open full chat.php button -->
      <a id="open-full-chat" href="#" class="open-full-btn" title="Open full chat">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        OPEN
      </a>
      <div class="enc-pill">
        <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1l9 4v6c0 5.25-3.75 10.15-9 11.5C6.75 21.15 3 16.25 3 11V5l9-4z"/></svg>
        AES-256
      </div>
    </div>
    <div class="conv-msgs" id="conv-msgs"></div>
    <div class="locked" id="conv-locked" style="display:none">
      <div class="lock-card">
        <svg width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        <h3>NOT CONNECTED</h3>
        <p>You need to be friends before you can send messages.</p>
        <a href="friends.php">View Friend Requests</a>
      </div>
    </div>
    <div class="conv-inp" id="conv-inp">
      <form class="typing-row" id="chat-form">
        <input type="hidden" id="conv-uid" name="incoming_id" value="">
        <textarea name="message" id="chat-msg" rows="1" placeholder="Type a message…"
          oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,80)+'px'"
          autocomplete="off"></textarea>
        <button type="submit" class="send-btn">
          <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        </button>
      </form>
    </div>
  </div>
</div>

</div>

<script>
const sInput = document.getElementById('s-input');
const sClear = document.getElementById('s-clear');
const srp    = document.getElementById('srp');
const srpLoader = document.getElementById('srp-loader');
const srpEmpty  = document.getElementById('srp-empty');
const chatListEl = document.getElementById('chat-list');

sInput.addEventListener('input', debounce(doSearch, 280));
sClear.addEventListener('click', clearSearch);
document.addEventListener('keydown', e => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); sInput.focus(); }
  if (e.key === 'Escape') clearSearch();
});

function clearSearch() {
  sInput.value = ''; sClear.style.display = 'none';
  srp.classList.remove('open'); chatListEl.style.display = '';
  srpLoader.style.display = 'none'; srpEmpty.style.display = 'none';
  [...srp.querySelectorAll('.sri')].forEach(e => e.remove());
}

function doSearch() {
  const q = sInput.value.trim();
  sClear.style.display = q ? 'block' : 'none';
  if (!q) { clearSearch(); return; }
  chatListEl.style.display = 'none';
  srp.classList.add('open');
  srpEmpty.style.display = 'none';
  srpLoader.style.display = 'block';
  [...srp.querySelectorAll('.sri')].forEach(e => e.remove());
  fetch('api/search_users.php?searchTerm=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(data => {
      srpLoader.style.display = 'none';
      if (!data || !data.length) { srpEmpty.textContent = 'No results for "' + q + '"'; srpEmpty.style.display = 'block'; return; }
      data.forEach(u => {
        const div = document.createElement('div');
        div.className = 'sri';
        let badge = '';
        if (u.rel === 'friend') badge = `<a class="rel-badge friend" href="conversation.php?user_id=${u.unique_id}" onclick="event.stopPropagation()">💬 MSG</a>`;
        else if (u.rel === 'pending') badge = `<span class="rel-badge pending">PENDING</span>`;
        else badge = `<a class="rel-badge add" href="api/freq.php?to_user_id=${u.unique_id}" onclick="event.stopPropagation()">+ ADD</a>`;
        div.innerHTML = `<img class="av" src="api/images/pfp/${u.img}" alt="" onerror="this.src='api/images/pfp/logo.jpg'"><div class="sri-info"><div class="sri-name">${esc(u.username)}</div><div class="sri-tag">${esc(u.username + u.hastag)}</div></div>${badge}`;
        div.addEventListener('click', e => {
          if (e.target.closest('.rel-badge')) return;
          if (u.rel === 'friend') window.location.href = 'conversation.php?user_id=' + u.unique_id;
          else window.location.href = 'profile.php?user_id=' + u.unique_id;
        });
        srp.insertBefore(div, srpEmpty);
      });
    })
    .catch(() => { srpLoader.style.display = 'none'; srpEmpty.textContent = 'Search failed.'; srpEmpty.style.display = 'block'; });
}

/* Conversation */
let pollTimer = null, curUID = null;

function openConv(el) {
  openConvById(el.dataset.uid, el.dataset.name, el.dataset.img, el.dataset.status || 'Active', el.dataset.rel);
  document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}

function openConvById(uid, name, img, status, rel) {
  curUID = uid;
  document.getElementById('welcome').classList.add('hidden');
  document.getElementById('conv').classList.add('show');
  document.getElementById('conv-avatar').src = 'api/images/pfp/' + img;
  document.getElementById('conv-name').textContent = name;
  document.getElementById('conv-status').textContent = status;
  document.getElementById('conv-uid').value = uid;
  document.getElementById('open-full-chat').href = 'conversation.php?user_id=' + uid;
  const isFriend = (rel === 'friend' || rel === undefined);
  document.getElementById('conv-locked').style.display = isFriend ? 'none' : 'flex';
  document.getElementById('conv-inp').style.display = isFriend ? '' : 'none';
  document.getElementById('conv-msgs').style.display = isFriend ? '' : 'none';
  if (isFriend) { fetchMsgs(uid); clearInterval(pollTimer); pollTimer = setInterval(() => fetchMsgs(uid), 1500); }
  document.getElementById('chat-msg').focus();
}

function closeConv() {
  clearInterval(pollTimer); curUID = null;
  document.getElementById('welcome').classList.remove('hidden');
  document.getElementById('conv').classList.remove('show');
  document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
}

function fetchMsgs(uid) {
  const box = document.getElementById('conv-msgs');
  const atBot = box.scrollHeight - box.scrollTop - box.clientHeight < 60;
  fetch('api/load-messages.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'incoming_id=' + uid })
    .then(r => r.text()).then(html => { box.innerHTML = html; if (atBot) box.scrollTop = box.scrollHeight; });
}

document.getElementById('chat-form').addEventListener('submit', e => {
  e.preventDefault();
  const ta = document.getElementById('chat-msg');
  if (!ta.value.trim()) return;
  fetch('api/send-message.php', { method: 'POST', body: new FormData(document.getElementById('chat-form')) })
    .then(() => { ta.value = ''; ta.style.height = 'auto'; if (curUID) fetchMsgs(curUID); });
});
document.getElementById('chat-msg').addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); document.getElementById('chat-form').dispatchEvent(new Event('submit')); }
});

// Auto-open from URL
(function() {
  const p = new URLSearchParams(location.search);
  const oUID = p.get('open_uid');
  if (oUID) {
    const oName = decodeURIComponent(p.get('name') || '');
    const oImg = decodeURIComponent(p.get('img') || 'logo.jpg');
    openConvById(oUID, oName, oImg, 'Active', 'friend');
    const el = document.querySelector('.chat-item[data-uid="' + oUID + '"]');
    if (el) { document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active')); el.classList.add('active'); }
  }
})();

function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    
    
    /* Mobile Sidebar Toggle */
function openConv(el) {
  openConvById(
    el.dataset.uid,
    el.dataset.name,
    el.dataset.img,
    el.dataset.status || 'Active',
    el.dataset.rel
  );

  document.querySelector('.sidebar').classList.add('hide');

  document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}

function closeConv() {
  clearInterval(pollTimer);
  curUID = null;

  document.getElementById('welcome').classList.remove('hidden');
  document.getElementById('conv').classList.remove('show');

  document.querySelector('.sidebar').classList.remove('hide');

  document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
}
</script>
</body>
</html>
