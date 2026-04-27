<?php
session_start();
$_NAV['current_pos'] = 'myprofile';
include_once "api/config.php";

if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}

include_once "header.php";
include_once "api/update_pfp.php";
include_once "api/update_profile.php";

$sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
if (mysqli_num_rows($sql) > 0) {
    $row = mysqli_fetch_assoc($sql);
} else {
    header("location: users.php");
    exit();
}

$sql2 = "SELECT * FROM friends WHERE friend_id = {$_SESSION['unique_id']}";
$query = mysqli_query($conn, $sql2);
$ResultCount = mysqli_num_rows($query);
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
body{font-family:'Sora',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(0,194,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,194,255,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;z-index:0}

.page{position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 16px}

/* Profile Card */
.profile-card{width:100%;max-width:420px;background:var(--panel);border:1px solid var(--border);border-radius:28px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.5),0 0 0 1px rgba(0,194,255,0.05);position:relative}
.profile-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--accent),var(--green),transparent);z-index:10}

/* Cover */
.cover{height:110px;background:linear-gradient(135deg,#0c2d45,#0f4060,#0a2d3d,#00C2FF20);position:relative;overflow:hidden}
.cover::after{content:'';position:absolute;inset:0;background:repeating-linear-gradient(45deg,transparent,transparent 14px,rgba(255,255,255,0.015) 14px,rgba(255,255,255,0.015) 15px)}
.cover-glow{position:absolute;top:-30px;left:50%;transform:translateX(-50%);width:300px;height:200px;background:radial-gradient(ellipse,rgba(0,194,255,0.15) 0%,transparent 65%);pointer-events:none}

/* Avatar */
.avatar-zone{position:relative;margin:-56px auto 0;width:112px;z-index:5}
.avatar-zone img{width:112px;height:112px;border-radius:50%;object-fit:cover;border:4px solid var(--panel);box-shadow:0 0 24px rgba(0,194,255,0.25);display:block;transition:transform .3s}
.avatar-zone:hover img{transform:scale(1.03)}
.edit-pic-btn{position:absolute;bottom:2px;right:2px;width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--blue));border:3px solid var(--panel);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;transition:all .25s;box-shadow:0 0 12px rgba(0,194,255,0.4)}
.edit-pic-btn:hover{transform:scale(1.15) rotate(15deg);box-shadow:0 0 20px rgba(0,194,255,0.6)}

/* Body */
.card-body{padding:16px 24px 28px;text-align:center}
.username{font-family:'Orbitron',monospace;font-size:20px;font-weight:900;color:var(--text);letter-spacing:1px;margin-bottom:8px}
.status-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.28);border-radius:999px;padding:5px 14px;font-size:11px;color:var(--green);font-weight:600;margin-bottom:18px}
.status-dot{width:7px;height:7px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);animation:glow 2s ease-in-out infinite}
@keyframes glow{0%,100%{opacity:1}50%{opacity:.4}}

/* About box */
.about-box{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:14px 18px;margin:0 auto 22px;max-width:320px;text-align:left;position:relative}
.about-box::before{content:'ABOUT';position:absolute;top:-9px;left:14px;background:var(--card);padding:0 8px;font-family:'Orbitron',monospace;font-size:9px;font-weight:700;color:var(--text3);letter-spacing:2px}
.about-text{font-size:13px;color:var(--text2);line-height:1.7}

/* Stats */
.stats{display:flex;gap:10px;justify-content:center;margin-bottom:22px}
.stat{background:rgba(0,194,255,0.05);border:1px solid var(--border);border-radius:14px;padding:12px 20px;text-align:center}
.stat-n{font-family:'Orbitron',monospace;font-size:22px;font-weight:900;color:var(--accent)}
.stat-l{font-size:10px;color:var(--text3);letter-spacing:.5px;margin-top:2px}

/* Buttons */
.btn-row{display:flex;justify-content:center;gap:12px;flex-wrap:wrap}
.btn-edit{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:linear-gradient(135deg,rgba(0,194,255,0.15),rgba(37,99,235,0.15));border:1px solid rgba(0,194,255,0.35);border-radius:13px;color:var(--accent);font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:.8px;cursor:pointer;text-decoration:none;transition:all .25s}
.btn-edit:hover{background:linear-gradient(135deg,rgba(0,194,255,0.28),rgba(37,99,235,0.25));border-color:rgba(0,194,255,0.6);box-shadow:0 0 20px rgba(0,194,255,0.3),0 4px 16px rgba(0,0,0,0.3);transform:translateY(-2px) scale(1.02)}
.btn-edit:active{transform:scale(0.97)}
.btn-home{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:linear-gradient(135deg,rgba(34,197,94,0.15),rgba(16,163,74,0.12));border:1px solid rgba(34,197,94,0.35);border-radius:13px;color:var(--green);font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:.8px;cursor:pointer;text-decoration:none;transition:all .25s}
.btn-home:hover{background:linear-gradient(135deg,rgba(34,197,94,0.28),rgba(16,163,74,0.22));border-color:rgba(34,197,94,0.6);box-shadow:0 0 20px rgba(34,197,94,0.3),0 4px 16px rgba(0,0,0,0.3);transform:translateY(-2px) scale(1.02)}
.btn-home:active{transform:scale(0.97)}

/* Modal */
.modal-overlay{position:fixed;inset:0;z-index:999;background:rgba(0,0,0,0.8);backdrop-filter:blur(12px);display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;pointer-events:none;transition:opacity .3s}
.modal-overlay.open{opacity:1;pointer-events:all}
.modal-box{width:100%;max-width:440px;background:var(--panel);border:1px solid var(--border);border-radius:24px;overflow:hidden;position:relative;transform:translateY(20px) scale(0.97);transition:all .3s;box-shadow:0 32px 80px rgba(0,0,0,0.7)}
.modal-overlay.open .modal-box{transform:translateY(0) scale(1)}
.modal-box::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--accent),var(--green),transparent)}
.modal-glow-1{position:absolute;top:-50px;left:-50px;width:160px;height:160px;background:radial-gradient(circle,rgba(0,194,255,0.12) 0%,transparent 70%);pointer-events:none}
.modal-glow-2{position:absolute;bottom:-50px;right:-50px;width:160px;height:160px;background:radial-gradient(circle,rgba(34,197,94,0.1) 0%,transparent 70%);pointer-events:none}
.modal-header{padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1}
.modal-title{font-family:'Orbitron',monospace;font-size:16px;font-weight:900;color:var(--text);letter-spacing:1px}
.modal-sub{font-size:11px;color:var(--text3);margin-top:2px}
.modal-close{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,0.06);border:1px solid var(--border);color:var(--text2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;transition:all .2s;flex-shrink:0}
.modal-close:hover{background:rgba(255,95,109,0.15);border-color:rgba(255,95,109,0.35);color:var(--danger)}
.modal-body{padding:20px 24px 24px;position:relative;z-index:1}

/* Form fields */
.field{margin-bottom:16px}
.field label{display:block;font-family:'Orbitron',monospace;font-size:10px;font-weight:700;color:var(--text3);letter-spacing:1.5px;margin-bottom:8px;text-transform:uppercase}
.field-wrap{position:relative}
.field-wrap input,.field-wrap textarea{width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:13px;padding:12px 44px 12px 16px;color:var(--text);font-family:'Sora',sans-serif;font-size:13px;outline:none;transition:all .25s}
.field-wrap input:focus,.field-wrap textarea:focus{border-color:rgba(0,194,255,0.5);box-shadow:0 0 0 3px rgba(0,194,255,0.08);background:#0c1d2e}
.field-wrap input::placeholder,.field-wrap textarea::placeholder{color:var(--text3)}
.field-wrap textarea{resize:none;line-height:1.6;padding-right:16px}
.field-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--accent);opacity:.7;pointer-events:none;font-size:15px}

/* Preview image */
.pfp-preview-wrap{display:flex;justify-content:center;margin-bottom:18px}
.pfp-preview-wrap img{width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid rgba(0,194,255,0.35);box-shadow:0 0 20px rgba(0,194,255,0.2)}

/* File input */
.file-input-wrap{position:relative;background:var(--card);border:1.5px dashed rgba(0,194,255,0.3);border-radius:13px;padding:14px 16px;text-align:center;cursor:pointer;transition:all .25s}
.file-input-wrap:hover{border-color:rgba(0,194,255,0.6);background:#0c1d2e}
.file-input-wrap input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.file-input-label{font-size:12px;color:var(--text3);display:flex;flex-direction:column;align-items:center;gap:6px;pointer-events:none}
.file-input-label svg{color:var(--accent);opacity:.7}
.file-input-name{font-size:11px;color:var(--accent);margin-top:4px;display:none}

/* Modal action buttons */
.modal-actions{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:20px}
.btn-cancel{display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:rgba(255,95,109,0.08);border:1px solid rgba(255,95,109,0.3);border-radius:13px;color:var(--danger);font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:.8px;cursor:pointer;transition:all .25s}
.btn-cancel:hover{background:rgba(255,95,109,0.2);border-color:rgba(255,95,109,0.55);box-shadow:0 0 16px rgba(255,95,109,0.25);transform:translateY(-1px)}
.btn-save{display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:linear-gradient(135deg,rgba(34,197,94,0.18),rgba(16,163,74,0.15));border:1px solid rgba(34,197,94,0.35);border-radius:13px;color:var(--green);font-family:'Orbitron',monospace;font-size:11px;font-weight:700;letter-spacing:.8px;cursor:pointer;transition:all .25s}
.btn-save:hover{background:linear-gradient(135deg,rgba(34,197,94,0.3),rgba(16,163,74,0.25));border-color:rgba(34,197,94,0.6);box-shadow:0 0 18px rgba(34,197,94,0.3);transform:translateY(-1px)}
</style>

<?php include_once "api/alerts.php"; ?>

<div class="page">
  <div class="profile-card">

    <!-- Cover -->
    <div class="cover">
      <div class="cover-glow"></div>
    </div>

    <!-- Avatar -->
    <div style="display:flex;justify-content:center">
      <div class="avatar-zone">
        <img id="main-avatar"
             src="api/images/pfp/<?php echo htmlspecialchars($row['img']); ?>"
             alt="Profile"
             onerror="this.src='api/images/pfp/logo.jpg'">
        <button class="edit-pic-btn"
                onclick="document.getElementById('pfp-modal').classList.add('open')"
                title="Change photo">✎</button>
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <div class="username"><?php echo htmlspecialchars($row['username']); ?></div>
      <div class="status-pill">
        <span class="status-dot"></span>
        Active User
      </div>

      <div class="about-box">
        <p class="about-text"><?php echo nl2br(htmlspecialchars($row['about'] ?: 'No bio yet.')); ?></p>
      </div>

      <div class="stats">
        <div class="stat">
          <div class="stat-n"><?php echo $ResultCount; ?></div>
          <div class="stat-l">FRIENDS</div>
        </div>
        <div class="stat">
          <div class="stat-n">●</div>
          <div class="stat-l">ONLINE</div>
        </div>
      </div>

      <div class="btn-row">
        <button class="btn-edit" onclick="document.getElementById('edit-modal').classList.add('open')">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          EDIT PROFILE
        </button>
        <a href="users.php" class="btn-home">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          HOME
        </a>
      </div>
    </div>
  </div>
</div>

<!-- EDIT PROFILE MODAL -->
<div class="modal-overlay" id="edit-modal">
  <div class="modal-box">
    <div class="modal-glow-1"></div>
    <div class="modal-glow-2"></div>
    <div class="modal-header">
      <div>
        <div class="modal-title">EDIT PROFILE</div>
        <div class="modal-sub">Update your information</div>
      </div>
      <button class="modal-close" onclick="document.getElementById('edit-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="field">
          <label>Email Address</label>
          <div class="field-wrap">
            <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Enter your email" required>
            <span class="field-icon">
              <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </span>
          </div>
        </div>
        <div class="field">
          <label>Username</label>
          <div class="field-wrap">
            <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" maxlength="30" placeholder="Enter username" required onkeyup="this.value=this.value.replace(/\s/g,'')">
            <span class="field-icon" style="color:#ec4899;font-size:16px;font-weight:700">@</span>
          </div>
        </div>
        <div class="field">
          <label>About Me</label>
          <div class="field-wrap">
            <textarea name="about" rows="3" maxlength="150" placeholder="Tell people about yourself…"><?php echo htmlspecialchars($row['about']); ?></textarea>
          </div>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="document.getElementById('edit-modal').classList.remove('open')">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            CANCEL
          </button>
          <button type="submit" class="btn-save" name="update_profile">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            SAVE
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT PHOTO MODAL -->
<div class="modal-overlay" id="pfp-modal">
  <div class="modal-box" style="max-width:360px">
    <div class="modal-header">
      <div>
        <div class="modal-title">CHANGE PHOTO</div>
        <div class="modal-sub">Upload a new profile picture</div>
      </div>
      <button class="modal-close" onclick="document.getElementById('pfp-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <form method="POST" enctype="multipart/form-data">

        <!-- Live preview -->
        <div class="pfp-preview-wrap">
          <img id="preview"
               src="api/images/pfp/<?php echo htmlspecialchars($row['img']); ?>"
               onerror="this.src='api/images/pfp/logo.jpg'"
               alt="Preview">
        </div>

        <div class="field">
          <label>Choose Image</label>
          <div class="file-input-wrap">
            <input type="file" id="pfp" name="image" accept="image/*">
            <div class="file-input-label">
              <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span>Click to select a photo</span>
              <span class="file-input-name" id="file-name-display"></span>
            </div>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="document.getElementById('pfp-modal').classList.remove('open')">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            CANCEL
          </button>
          <button type="submit" class="btn-save" name="update_pfp">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            UPLOAD
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
window.onload = function () {

  /* ── Modal backdrop close ── */
  document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) overlay.classList.remove('open');
    });
  });

  /* ── Escape key closes modals ── */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(function (m) {
        m.classList.remove('open');
      });
    }
  });

  /* ── Profile picture live preview ── */
  var fileInput   = document.getElementById('pfp');
  var preview     = document.getElementById('preview');
  var mainAvatar  = document.getElementById('main-avatar');
  var fileNameEl  = document.getElementById('file-name-display');

  if (fileInput && preview) {
    fileInput.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          preview.src    = e.target.result;
          mainAvatar.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);

        /* Show filename */
        if (fileNameEl) {
          fileNameEl.textContent = this.files[0].name;
          fileNameEl.style.display = 'block';
        }
      }
    });
  }

};
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.4/flowbite.min.js"></script>
</body>
</html>