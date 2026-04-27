<?php
session_start();
$_NAV['current_pos'] = 'profile';
include_once "api/config.php";

if (!isset($_SESSION['unique_id'])) {
    header("location: auth/login.php");
    exit();
}

$session_uid = $_SESSION['unique_id'];
$user_id     = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id === (int)$session_uid) {
    header("location: myprofile.php");
    exit();
}

$sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
if (mysqli_num_rows($sql) === 0) {
    header("location: users.php");
    exit();
}
$row = mysqli_fetch_assoc($sql);

$fc_q = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM friends WHERE friend_id = {$user_id}");
$fc   = mysqli_fetch_assoc($fc_q)['cnt'];

$is_friend_q = mysqli_query($conn,
    "SELECT * FROM friends WHERE unique_id = {$user_id} AND friend_id = {$session_uid}");
$is_friend = mysqli_num_rows($is_friend_q) > 0;

$req_q = mysqli_query($conn,
    "SELECT * FROM friend_req
     WHERE (from_req_id = {$user_id} AND to_req_id = {$session_uid})
        OR (from_req_id = {$session_uid} AND to_req_id = {$user_id})");
$has_req = mysqli_num_rows($req_q) > 0;
$req_row = $has_req ? mysqli_fetch_assoc($req_q) : null;
?>
<?php include_once "header.php"; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Sora:wght@300;400;500;600;700&display=swap');
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#050B14;--panel:#0B1624;--card:#101D2E;
  --accent:#00C2FF;--blue:#2563EB;--green:#22C55E;
  --border:rgba(0,194,255,0.18);--text:#FFFFFF;
  --text2:#94A3B8;--text3:#64748B;--danger:#FF5F6D;
}
body{font-family:'Sora',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(0,194,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(0,194,255,0.025) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;z-index:0}

.page{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 16px;position:relative;z-index:1}
.page::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:700px;height:400px;background:radial-gradient(ellipse,rgba(0,194,255,0.07) 0%,transparent 65%);pointer-events:none}

/* Back link */
.back-link{display:inline-flex;align-items:center;gap:8px;color:var(--text3);font-size:13px;text-decoration:none;margin-bottom:24px;transition:all .2s;font-family:'Orbitron',monospace;font-weight:600;letter-spacing:.5px;padding:8px 16px;border:1px solid var(--border);border-radius:10px;background:rgba(0,194,255,0.04)}
.back-link:hover{color:var(--accent);border-color:rgba(0,194,255,0.35);background:rgba(0,194,255,0.08);transform:translateX(-3px)}
.back-link svg{transition:transform .2s}
.back-link:hover svg{transform:translateX(-4px)}

/* Profile card */
.profile-card{background:var(--panel);border:1px solid var(--border);border-radius:28px;width:100%;max-width:440px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.5);position:relative}
.profile-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--accent),transparent);z-index:10}

/* Banner */
.profile-banner{height:120px;background:linear-gradient(135deg,#0c2333,#0a3050,#0d1f32,#071825);position:relative;overflow:hidden}
.profile-banner::after{content:'';position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 14px,rgba(255,255,255,0.015) 14px,rgba(255,255,255,0.015) 15px)}
.banner-glow{position:absolute;top:-30px;left:50%;transform:translateX(-50%);width:400px;height:250px;background:radial-gradient(ellipse,rgba(0,194,255,0.12) 0%,transparent 65%);pointer-events:none}

/* Avatar */
.avatar-wrap{position:relative;margin:-52px auto 0;width:104px;z-index:5;display:block}
.avatar-wrap img{width:104px;height:104px;border-radius:50%;object-fit:cover;border:4px solid var(--panel);display:block;box-shadow:0 0 24px rgba(0,194,255,0.2);transition:box-shadow .3s}
.avatar-wrap:hover img{box-shadow:0 0 36px rgba(0,194,255,0.35)}
.status-dot{position:absolute;bottom:7px;right:7px;width:15px;height:15px;border-radius:50%;background:var(--green);border:3px solid var(--panel);box-shadow:0 0 8px var(--green)}

/* Info */
.profile-info{text-align:center;padding:14px 28px 28px}
.profile-name{font-family:'Orbitron',monospace;font-size:22px;font-weight:900;color:var(--text);margin-bottom:6px;letter-spacing:.5px}
.profile-handle{font-size:13px;color:var(--text3);margin-bottom:14px}
.profile-about{font-size:13px;color:var(--text2);line-height:1.7;margin-bottom:20px;white-space:pre-wrap;padding:0 4px}

/* Friend count */
.friends-chip{display:inline-flex;align-items:center;gap:7px;font-size:12px;color:var(--accent);font-weight:600;background:rgba(0,194,255,0.08);border:1px solid rgba(0,194,255,0.2);border-radius:999px;padding:5px 14px;margin-bottom:24px;font-family:'Orbitron',monospace;letter-spacing:.5px}

/* Action buttons row */
.action-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}

/* Base button */
.btn{display:inline-flex;align-items:center;gap:9px;padding:12px 24px;border-radius:14px;font-family:'Orbitron',monospace;font-size:12px;font-weight:700;border:none;cursor:pointer;text-decoration:none;transition:all .3s;letter-spacing:.7px;position:relative;overflow:hidden}
.btn::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.08),transparent);opacity:0;transition:opacity .2s}
.btn:hover::after{opacity:1}

/* Message button — Cyan gradient */
.btn-message{background:linear-gradient(135deg,rgba(0,194,255,0.18),rgba(37,99,235,0.18));border:1px solid rgba(0,194,255,0.35);color:var(--accent)}
.btn-message:hover{background:linear-gradient(135deg,rgba(0,194,255,0.32),rgba(37,99,235,0.28));border-color:rgba(0,194,255,0.65);box-shadow:0 0 22px rgba(0,194,255,0.3),0 6px 20px rgba(0,0,0,0.3);transform:translateY(-3px) scale(1.03)}
.btn-message:active{transform:scale(0.97)}

/* Add friend button — Blue */
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--blue));color:#fff;box-shadow:0 0 20px rgba(0,194,255,0.25)}
.btn-primary:hover{transform:translateY(-3px) scale(1.03);box-shadow:0 0 32px rgba(0,194,255,0.45),0 8px 24px rgba(0,0,0,0.3)}

/* Friends status — green */
.btn-friends{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:var(--green);cursor:default}

/* Pending */
.btn-pending{background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#fbbf24;cursor:default}

/* Cancel — red */
.btn-cancel{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171}
.btn-cancel:hover{background:rgba(239,68,68,0.2);border-color:rgba(239,68,68,0.55);transform:translateY(-2px);box-shadow:0 0 16px rgba(239,68,68,0.2)}

/* Divider */
.divider{height:1px;background:var(--border);margin:20px 0}

/* Stats row */
.stats-row{display:flex;border-top:1px solid var(--border)}
.stat-item{flex:1;padding:16px 8px;text-align:center;border-right:1px solid var(--border)}
.stat-item:last-child{border-right:none}
.stat-num{font-family:'Orbitron',monospace;font-size:22px;font-weight:900;color:var(--text)}
.stat-label{font-size:10px;color:var(--text3);margin-top:3px;text-transform:uppercase;letter-spacing:1px}
.stat-item.highlight .stat-num{color:var(--accent)}

/* Alert */
.alert{padding:10px 16px;border-radius:12px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.alert-success{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:var(--green)}
.alert-error{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#f87171}
</style>

<div class="page">
  <a href="users.php" class="back-link">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Back to Chats
  </a>

  <?php if (isset($_SESSION['alertSuccess'])): ?>
    <div class="alert alert-success">✓ <?php echo htmlspecialchars($_SESSION['alertSuccess']); unset($_SESSION['alertSuccess']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['alertError'])): ?>
    <div class="alert alert-error">✕ <?php echo htmlspecialchars($_SESSION['alertError']); unset($_SESSION['alertError']); ?></div>
  <?php endif; ?>

  <div class="profile-card">
    <div class="profile-banner">
      <div class="banner-glow"></div>
    </div>

    <div style="display:flex;justify-content:center">
      <div class="avatar-wrap">
        <img src="api/images/pfp/<?php echo htmlspecialchars($row['img']); ?>" alt="" onerror="this.src='api/images/pfp/logo.jpg'">
        <span class="status-dot"></span>
      </div>
    </div>

    <div class="profile-info">
      <div class="profile-name"><?php echo htmlspecialchars($row['full_username'] ?: $row['username']); ?></div>
      <div class="profile-handle">@<?php echo htmlspecialchars($row['username']); ?></div>

      <?php if (!empty($row['about'])): ?>
        <div class="profile-about"><?php echo nl2br(htmlspecialchars($row['about'])); ?></div>
      <?php endif; ?>

      <div class="friends-chip">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <?php echo $fc; ?> Connections
      </div>

      <div class="action-row">

        <?php if ($is_friend): ?>
          <!-- Message button — goes to chat.php -->
          <a href="conversation.php?user_id=<?php echo $user_id; ?>" class="btn btn-message">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            SEND MESSAGE
          </a>
          <span class="btn btn-friends">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            CONNECTED
          </span>

        <?php elseif ($has_req): ?>
          <span class="btn btn-pending">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo htmlspecialchars($req_row['status'] ?? 'Pending'); ?>
          </span>
          <a href="api/freq.php?cancel=<?php echo $user_id; ?>" class="btn btn-cancel">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            CANCEL
          </a>

        <?php else: ?>
          <a href="api/freq.php?to_user_id=<?php echo $user_id; ?>" class="btn btn-primary">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
            SEND REQUEST
          </a>
        <?php endif; ?>

      </div>
    </div>

    <div class="stats-row">
      <div class="stat-item highlight">
        <div class="stat-num"><?php echo $fc; ?></div>
        <div class="stat-label">Friends</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" style="color:var(--green)">●</div>
        <div class="stat-label">Online</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" style="color:rgba(0,194,255,0.6);font-size:16px">AES</div>
        <div class="stat-label">Encrypted</div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
