<?php
session_start();
$_NAV['current_pos'] = 'friend';
include_once "api/config.php";
if (!isset($_SESSION['unique_id'])) { header("location: auth/login.php"); exit(); }
$me = $_SESSION['unique_id'];

$req_cnt = mysqli_num_rows(mysqli_query($conn,"SELECT req_id FROM friend_req WHERE to_req_id={$me}"));
$fr_sql = mysqli_query($conn,"SELECT * FROM friends LEFT JOIN users ON users.unique_id = friends.unique_id WHERE friend_id={$me} ORDER BY id DESC");
$f_cnt2 = mysqli_num_rows($fr_sql);
?>
<?php include_once "header.php"; ?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Sora:wght@300;400;500;600;700&display=swap');
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#050B14;--panel:#0B1624;--card:#101D2E;
  --accent:#00C2FF;--blue:#2563EB;--green:#22C55E;
  --border:rgba(0,194,255,0.18);--hover:rgba(0,194,255,0.04);
  --danger:#FF5F6D;--text:#FFFFFF;--text2:#94A3B8;--text3:#64748B;
}
body{font-family:'Sora',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(0,194,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(0,194,255,0.025) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;z-index:0}

.page{max-width:760px;margin:0 auto;padding:32px 16px 80px;position:relative;z-index:1;display:flex;flex-direction:column;gap:20px}

/* ── TOPBAR ── */
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}

/* Advanced Back Button */
.back-btn{display:inline-flex;align-items:center;gap:10px;padding:11px 22px;background:linear-gradient(135deg,rgba(0,194,255,0.1),rgba(37,99,235,0.1));border:1px solid rgba(0,194,255,0.3);border-radius:14px;color:var(--accent);font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:1px;text-decoration:none;transition:all .3s;position:relative;overflow:hidden}
.back-btn::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,194,255,0.06),transparent);opacity:0;transition:opacity .2s}
.back-btn:hover::before{opacity:1}
.back-btn:hover{background:linear-gradient(135deg,rgba(0,194,255,0.2),rgba(37,99,235,0.18));border-color:rgba(0,194,255,0.6);box-shadow:0 0 24px rgba(0,194,255,0.25),0 6px 20px rgba(0,0,0,0.3);transform:translateX(-3px) translateY(-2px)}
.back-btn:active{transform:scale(0.96)}
.back-btn svg{transition:transform .25s}
.back-btn:hover svg{transform:translateX(-4px)}

/* Page header */
.page-header{position:relative}
.page-title{font-family:'Orbitron',monospace;font-size:26px;font-weight:900;color:var(--text);letter-spacing:3px;line-height:1}
.page-title span{color:var(--accent);position:relative}
.page-title span::after{content:'';position:absolute;bottom:-4px;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--accent),transparent)}
.page-sub{font-size:13px;color:var(--text3);letter-spacing:.5px;margin-top:10px}

/* ── SEARCH PANEL ── */
.search-panel{background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:20px;position:relative;overflow:hidden}
.search-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--accent),var(--green),transparent)}
.sp-label{font-family:'Orbitron',monospace;font-size:10px;font-weight:700;color:var(--accent);letter-spacing:2px;text-transform:uppercase;margin-bottom:14px;display:flex;align-items:center;gap:8px}
.sp-row{display:flex;gap:10px}
.sp-input{flex:1;background:var(--card);border:1.5px solid var(--border);border-radius:13px;padding:12px 18px;color:var(--text);font-family:'Sora',sans-serif;font-size:13px;outline:none;transition:all .25s}
.sp-input:focus{border-color:rgba(0,194,255,0.5);box-shadow:0 0 0 3px rgba(0,194,255,0.08);background:#0c1d2e}
.sp-input::placeholder{color:var(--text3)}
.sp-btn{background:linear-gradient(135deg,var(--accent),var(--blue));border:none;border-radius:13px;padding:12px 22px;color:#fff;font-family:'Orbitron',monospace;font-size:11px;font-weight:700;cursor:pointer;transition:all .25s;letter-spacing:1px;white-space:nowrap;box-shadow:0 0 20px rgba(0,194,255,0.25)}
.sp-btn:hover{transform:translateY(-2px);box-shadow:0 0 30px rgba(0,194,255,0.45);opacity:.9}
#sp-out{display:flex;flex-direction:column;gap:8px;margin-top:14px}
.sp-result{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:13px;background:var(--card);border:1px solid var(--border);transition:all .2s;animation:fadeUp .18s ease}
.sp-result:hover{border-color:rgba(0,194,255,0.3);background:#0c1d2e}
.sp-result img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid var(--border)}
.sp-result-info{flex:1;min-width:0}
.sp-result-name{font-weight:600;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sp-result-tag{font-size:11px;color:var(--text3);margin-top:1px}
.sp-result-btn{background:linear-gradient(135deg,rgba(0,194,255,0.12),rgba(37,99,235,0.12));border:1px solid rgba(0,194,255,0.28);border-radius:9px;padding:6px 14px;color:var(--accent);font-size:11px;font-weight:700;font-family:'Orbitron',monospace;text-decoration:none;letter-spacing:.5px;transition:all .2s;white-space:nowrap}
.sp-result-btn:hover{background:linear-gradient(135deg,rgba(0,194,255,0.22),rgba(37,99,235,0.22));box-shadow:0 0 12px rgba(0,194,255,0.2)}

/* ── SECTION HEADERS ── */
.sec-hdr{display:flex;align-items:center;justify-content:space-between;padding-bottom:12px;border-bottom:1px solid var(--border);margin-top:8px}
.sec-hdr-left{display:flex;align-items:center;gap:10px}
.sec-hdr h2{font-family:'Orbitron',monospace;font-size:12px;font-weight:700;color:var(--text2);letter-spacing:2px;text-transform:uppercase}
.sec-badge{background:linear-gradient(135deg,rgba(0,194,255,0.15),rgba(37,99,235,0.15));border:1px solid rgba(0,194,255,0.25);border-radius:999px;padding:3px 12px;font-size:11px;color:var(--accent);font-weight:700;font-family:'Orbitron',monospace}
.sec-badge.orange{background:rgba(245,158,11,0.12);border-color:rgba(245,158,11,0.3);color:#f59e0b}
.sec-badge.green{background:rgba(34,197,94,0.12);border-color:rgba(34,197,94,0.3);color:var(--green)}

/* ── CARDS ── */
.card{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:16px;display:flex;align-items:center;gap:14px;transition:all .2s;position:relative;overflow:hidden;animation:fadeUp .25s ease both}
.card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,194,255,0.015),transparent);pointer-events:none}
.card:hover{border-color:rgba(0,194,255,0.3);box-shadow:0 0 24px rgba(0,194,255,0.07),0 8px 24px rgba(0,0,0,0.2);transform:translateY(-1px)}
.card img{border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0;transition:border-color .2s}
.card:hover img{border-color:rgba(0,194,255,0.45)}
.card-info{flex:1;min-width:0}
.c-name{font-weight:700;font-size:14px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.c-tag{font-size:11px;color:var(--text3);margin-top:2px}
.c-meta{font-size:10px;margin-top:5px;display:flex;align-items:center;gap:5px;font-family:'Orbitron',monospace;letter-spacing:.5px}
.c-meta.req{color:var(--text3)}
.c-meta.online{color:var(--green)}
.c-meta.online::before{content:'';width:5px;height:5px;border-radius:50%;background:var(--green);box-shadow:0 0 5px var(--green)}

.card-actions{display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;justify-content:flex-end}

/* ── ACTION BUTTONS ── */
.btn{display:inline-flex;align-items:center;gap:6px;border-radius:11px;padding:9px 16px;font-size:11px;font-weight:700;font-family:'Orbitron',monospace;cursor:pointer;text-decoration:none;transition:all .25s;letter-spacing:.5px;border:none;white-space:nowrap}

/* Accept — Green */
.btn-accept{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:var(--green)}
.btn-accept:hover{background:rgba(34,197,94,0.22);border-color:rgba(34,197,94,0.55);box-shadow:0 0 16px rgba(34,197,94,0.25);transform:translateY(-2px)}

/* Decline — Red */
.btn-decline{background:rgba(255,95,109,0.08);border:1px solid rgba(255,95,109,0.25);color:var(--danger)}
.btn-decline:hover{background:rgba(255,95,109,0.2);border-color:rgba(255,95,109,0.5);box-shadow:0 0 16px rgba(255,95,109,0.2);transform:translateY(-2px)}

/* Profile — neutral */
.btn-profile{background:rgba(255,255,255,0.04);border:1px solid var(--border);color:var(--text3)}
.btn-profile:hover{background:var(--hover);color:var(--text);border-color:rgba(0,194,255,0.35);transform:translateY(-2px)}

/* Message — Cyan */
.btn-msg{background:linear-gradient(135deg,rgba(0,194,255,0.14),rgba(37,99,235,0.14));border:1px solid rgba(0,194,255,0.32);color:var(--accent)}
.btn-msg:hover{background:linear-gradient(135deg,rgba(0,194,255,0.26),rgba(37,99,235,0.22));border-color:rgba(0,194,255,0.6);box-shadow:0 0 18px rgba(0,194,255,0.25);transform:translateY(-2px)}

.new-badge{background:linear-gradient(135deg,var(--accent),var(--blue));color:#fff;border-radius:999px;padding:2px 8px;font-size:9px;font-weight:900;letter-spacing:.5px;margin-left:6px;box-shadow:0 0 8px rgba(0,194,255,0.4)}

/* Empty state */
.empty{text-align:center;padding:36px 20px;border:1px dashed rgba(0,194,255,0.12);border-radius:18px;color:var(--text3)}
.empty svg{margin:0 auto 14px;display:block;opacity:.2}
.empty p{font-size:13px;line-height:1.7}
.empty .hint{margin-top:14px;display:inline-flex;align-items:center;gap:6px;font-size:11px;color:var(--accent);font-weight:700;opacity:.8;font-family:'Orbitron',monospace;letter-spacing:.5px}

@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
</style>

<div class="page">

  <!-- Topbar -->
  <div class="topbar">
    <div class="page-header">
      <div class="page-title">MY <span>NETWORK</span></div>
      <div class="page-sub">Manage contacts &amp; incoming requests</div>
    </div>
    <!-- Advanced Back to Home button -->
    <a href="users.php" class="back-btn">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      HOME
    </a>
  </div>

  <!-- Search -->
  <div class="search-panel">
    <div class="sp-label">
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/></svg>
      FIND PEOPLE
    </div>
    <div class="sp-row">
      <input type="text" class="sp-input" id="sp-input" placeholder="Search by username or #hashtag…" autocomplete="off">
      <button class="sp-btn" onclick="spSearch()">SEARCH</button>
    </div>
    <div id="sp-out"></div>
  </div>

  <!-- Pending Requests -->
  <div class="sec-hdr">
    <div class="sec-hdr-left">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="rgba(0,194,255,0.7)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
      <h2>PENDING REQUESTS</h2>
    </div>
    <span class="sec-badge orange"><?php echo $req_cnt; ?></span>
  </div>

  <?php
  $req_sql = mysqli_query($conn,"SELECT * FROM friend_req LEFT JOIN users ON users.unique_id = friend_req.from_req_id WHERE to_req_id={$me} ORDER BY req_id DESC");
  if (mysqli_num_rows($req_sql) === 0) {
    echo '<div class="empty"><svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg><p>No pending friend requests.<br>People will appear here when they want to connect.</p></div>';
  } else {
    $idx = 0;
    while ($r = mysqli_fetch_assoc($req_sql)) {
      $idx++;
      echo '<div class="card" style="animation-delay:'.($idx*0.06).'s">
        <img src="api/images/pfp/'.htmlspecialchars($r['img']).'" width="52" height="52" alt="" onerror="this.src=\'api/images/pfp/logo.jpg\'">
        <div class="card-info">
          <div class="c-name">'.htmlspecialchars($r['username']).'</div>
          <div class="c-tag">'.htmlspecialchars($r['username'].$r['hastag']).'</div>
          <div class="c-meta req">
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Connection Request
          </div>
        </div>
        <div class="card-actions">
          <a href="api/freq.php?accept_req='.$r['from_req_id'].'" class="btn btn-accept">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            ACCEPT
          </a>
          <a href="api/freq.php?ignore_req='.$r['from_req_id'].'" class="btn btn-decline">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            DECLINE
          </a>
        </div>
      </div>';
    }
  }
  ?>

  <!-- Contacts -->
  <div class="sec-hdr">
    <div class="sec-hdr-left">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="rgba(0,194,255,0.7)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <h2>YOUR CONTACTS</h2>
    </div>
    <span class="sec-badge green"><?php echo $f_cnt2; ?></span>
  </div>

  <?php if ($f_cnt2 === 0): ?>
    <div class="empty">
      <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <p>No contacts yet.<br>Find people using the search above.</p>
    </div>
  <?php else:
    $i = 0;
    mysqli_data_seek($fr_sql, 0);
    while ($f = mysqli_fetch_assoc($fr_sql)):
      $isNew = $i < 3;
      $i++;
  ?>
    <div class="card" style="animation-delay:<?php echo ($i*0.05+0.1); ?>s">
      <img src="api/images/pfp/<?php echo htmlspecialchars($f['img']); ?>" width="52" height="52" alt="" onerror="this.src='api/images/pfp/logo.jpg'">
      <div class="card-info">
        <div class="c-name">
          <?php echo htmlspecialchars($f['username']); ?>
          <?php if($isNew): ?><span class="new-badge">NEW</span><?php endif; ?>
        </div>
        <div class="c-tag"><?php echo htmlspecialchars($f['username'].$f['hastag']); ?></div>
        <div class="c-meta online"><?php echo htmlspecialchars($f['status'] ?: 'Active'); ?></div>
      </div>
      <div class="card-actions">
        <a href="profile.php?user_id=<?php echo $f['unique_id']; ?>" class="btn btn-profile">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          PROFILE
        </a>
        <a href="conversation.php?user_id=<?php echo $f['unique_id']; ?>" class="btn btn-msg">
          <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          MESSAGE
        </a>
      </div>
    </div>
  <?php endwhile; endif; ?>

</div>

<script>
document.getElementById('sp-input').addEventListener('keydown', e => { if (e.key === 'Enter') spSearch(); });

function spSearch() {
  const q = document.getElementById('sp-input').value.trim();
  const out = document.getElementById('sp-out');
  if (!q) { out.innerHTML = ''; return; }
  out.innerHTML = '<p style="color:var(--text3);font-size:12px;padding:8px 0;display:flex;align-items:center;gap:6px"><span style="width:14px;height:14px;border:2px solid rgba(0,194,255,0.2);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite;display:inline-block"></span>Searching…</p>';
  fetch('api/search_users.php?searchTerm=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(data => {
      if (!data || !data.length) { out.innerHTML = '<p style="color:var(--text3);font-size:12px;padding:8px 0">No users found for "' + q + '"</p>'; return; }
      out.innerHTML = data.map(u => {
        let action = '';
        if (u.rel === 'friend') action = `<a class="sp-result-btn" href="conversation.php?user_id=${u.unique_id}" style="color:var(--green);border-color:rgba(34,197,94,0.3)">💬 MSG</a>`;
        else if (u.rel === 'pending') action = `<span style="color:#F59E0B;font-size:11px;font-weight:700;font-family:'Orbitron',monospace">⏳ PENDING</span>`;
        else action = `<a class="sp-result-btn" href="api/freq.php?to_user_id=${u.unique_id}">+ CONNECT</a>`;
        return `<div class="sp-result">
          <img src="api/images/pfp/${u.img}" width="40" height="40" alt="" onerror="this.src='api/images/pfp/logo.jpg'">
          <div class="sp-result-info">
            <div class="sp-result-name">${esc(u.username)}</div>
            <div class="sp-result-tag">${esc(u.username + u.hastag)}</div>
          </div>
          ${action}
        </div>`;
      }).join('');
    })
    .catch(() => { out.innerHTML = '<p style="color:var(--danger);font-size:12px;padding:8px 0">Search failed. Try again.</p>'; });
}

function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>

</body>
</html>
