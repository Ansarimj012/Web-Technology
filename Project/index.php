<?php include_once "header.php"; ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Sora:wght@300;400;500;600;700&display=swap');

:root {
  --bg: #050B14;
  --accent: #00C2FF;
  --green: #22C55E;
  --blue: #2563EB;
  --border: rgba(0,194,255,0.18);
  --text: #FFFFFF;
  --text2: #94A3B8;
  --text3: #64748B;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Sora', sans-serif;
  background: var(--bg);
  color: var(--text);
  overflow-x: hidden;
}

/* Grid BG */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image:
    linear-gradient(rgba(0,194,255,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,194,255,0.04) 1px, transparent 1px);
  background-size: 44px 44px;
  pointer-events: none;
  z-index: 0;
}

/* ── NAVBAR ── */
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  margin: 16px 16px 0;
  padding: 14px 24px;
  backdrop-filter: blur(20px);
  background: rgba(11, 22, 36, 0.8);
  border: 1px solid var(--border);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

.logo {
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
}

.logo-icon {
  width: 46px;
  height: 46px;
  border-radius: 14px;
  background: linear-gradient(135deg, var(--accent), var(--blue));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  box-shadow: 0 0 20px rgba(0,194,255,0.4);
  transition: transform 0.3s, box-shadow 0.3s;
}

.logo:hover .logo-icon {
  transform: rotate(8deg) scale(1.05);
  box-shadow: 0 0 30px rgba(0,194,255,0.6);
}

.logo-text .name {
  font-family: 'Orbitron', monospace;
  font-size: 20px;
  font-weight: 900;
  color: var(--text);
  letter-spacing: 2px;
}

.logo-text .tagline {
  font-size: 10px;
  color: var(--text3);
  letter-spacing: 2px;
  text-transform: uppercase;
}

/* Login button */
.nav-login {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 24px;
  background: linear-gradient(135deg, var(--accent), var(--blue));
  border: none;
  border-radius: 14px;
  color: #fff;
  font-family: 'Orbitron', monospace;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 0 20px rgba(0,194,255,0.3);
}

.nav-login:hover {
  transform: scale(1.05) translateY(-2px);
  box-shadow: 0 8px 30px rgba(0,194,255,0.5);
}

/* ── HERO ── */
.hero {
  position: relative;
  min-height: calc(100vh - 100px);
  display: flex;
  align-items: center;
  z-index: 1;
}

.hero-glow-left {
  position: absolute;
  top: -80px;
  left: -100px;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(0,194,255,0.1) 0%, transparent 65%);
  pointer-events: none;
}

.hero-glow-right {
  position: absolute;
  bottom: -80px;
  right: -100px;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(37,99,235,0.12) 0%, transparent 65%);
  pointer-events: none;
}

.hero-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 32px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
  width: 100%;
}

/* Left Content */
.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(0,194,255,0.08);
  border: 1px solid rgba(0,194,255,0.25);
  border-radius: 999px;
  padding: 6px 16px;
  font-size: 11px;
  color: var(--accent);
  font-family: 'Orbitron', monospace;
  letter-spacing: 1.5px;
  font-weight: 700;
  margin-bottom: 24px;
}

.hero-eyebrow::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 8px var(--green);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

.hero-title {
  font-family: 'Orbitron', monospace;
  font-size: clamp(36px, 5vw, 58px);
  font-weight: 900;
  line-height: 1.1;
  color: var(--text);
  margin-bottom: 20px;
  letter-spacing: -0.5px;
}

.hero-title .accent-word {
  background: linear-gradient(135deg, var(--accent), var(--green));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  display: block;
}

.hero-desc {
  font-size: 16px;
  color: var(--text2);
  line-height: 1.8;
  margin-bottom: 32px;
  max-width: 480px;
}

/* Feature Badges */
.badges {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 40px;
}

.badge {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 8px 16px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  transition: transform 0.2s;
  cursor: default;
}

.badge:hover { transform: translateY(-2px); }
.badge-cyan { background: rgba(0,194,255,0.1); color: var(--accent); border: 1px solid rgba(0,194,255,0.25); }
.badge-green { background: rgba(34,197,94,0.1); color: var(--green); border: 1px solid rgba(34,197,94,0.25); }
.badge-purple { background: rgba(139,92,246,0.1); color: #a78bfa; border: 1px solid rgba(139,92,246,0.25); }

/* ═══ HERO CTA BUTTON — ADVANCED ═══ */
.cta-wrap {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

/* Primary "Start Secure Chat" Button */
.btn-start-chat {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 16px 36px;
  background: transparent;
  border: none;
  border-radius: 18px;
  text-decoration: none;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  z-index: 1;
}

/* Animated gradient background */
.btn-start-chat::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #00C2FF, #22C55E, #2563EB, #00C2FF);
  background-size: 300% 300%;
  animation: gradientShift 4s ease infinite;
  border-radius: 18px;
  z-index: -2;
}

/* Glowing inner shadow */
.btn-start-chat::after {
  content: '';
  position: absolute;
  inset: 2px;
  background: linear-gradient(135deg, rgba(0,194,255,0.15), rgba(34,197,94,0.1));
  border-radius: 16px;
  z-index: -1;
  opacity: 0;
  transition: opacity 0.3s;
}

.btn-start-chat:hover::after { opacity: 1; }

@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.btn-start-chat:hover {
  transform: scale(1.05) translateY(-3px);
  box-shadow: 0 16px 40px rgba(0,194,255,0.4), 0 0 0 1px rgba(0,194,255,0.5);
}

.btn-start-chat:active {
  transform: scale(0.98);
}

/* Ripple effect */
.btn-start-chat .ripple {
  position: absolute;
  inset: 0;
  border-radius: 18px;
  background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.15) 0%, transparent 60%);
  animation: ripplePulse 2.5s ease-in-out infinite;
}

@keyframes ripplePulse {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50% { opacity: 0; transform: scale(1.1); }
}

.btn-start-chat .btn-icon {
  width: 38px;
  height: 38px;
  background: rgba(255,255,255,0.2);
  border-radius: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.3s;
}

.btn-start-chat:hover .btn-icon {
  transform: rotate(15deg) scale(1.1);
}

.btn-start-chat .btn-label {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.btn-start-chat .btn-label .main {
  font-family: 'Orbitron', monospace;
  font-size: 15px;
  font-weight: 900;
  color: #fff;
  letter-spacing: 1px;
  line-height: 1;
}

.btn-start-chat .btn-label .sub {
  font-size: 10px;
  color: rgba(255,255,255,0.75);
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-top: 3px;
}

/* Outer glow ring */
.btn-glow-ring {
  position: absolute;
  inset: -4px;
  border-radius: 22px;
  background: transparent;
  border: 1px solid rgba(0,194,255,0.4);
  animation: ringPulse 2.5s ease-in-out infinite;
  pointer-events: none;
}

@keyframes ringPulse {
  0%, 100% { opacity: 0.6; transform: scale(1); }
  50% { opacity: 0; transform: scale(1.05); }
}

/* Secondary "Learn More" link */
.btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--text3);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
  letter-spacing: 0.5px;
}

.btn-secondary:hover { color: var(--accent); }
.btn-secondary svg { transition: transform 0.2s; }
.btn-secondary:hover svg { transform: translateX(4px); }

/* ── RIGHT: GLASS CARD ── */
.hero-card {
  position: relative;
  background: rgba(11, 22, 36, 0.6);
  border: 1px solid var(--border);
  border-radius: 28px;
  padding: 32px;
  backdrop-filter: blur(20px);
  box-shadow: 0 24px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(0,194,255,0.05);
}

.hero-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--accent), var(--green), transparent);
  border-radius: 28px 28px 0 0;
}

.card-glow {
  position: absolute;
  top: -40px;
  right: -40px;
  width: 140px;
  height: 140px;
  background: radial-gradient(circle, rgba(0,194,255,0.15) 0%, transparent 70%);
  pointer-events: none;
}

.chat-preview {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.chat-bubble {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  animation: slideIn 0.5s ease both;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.chat-bubble.out {
  flex-direction: row-reverse;
  animation-delay: 0.1s;
}

.chat-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), var(--blue));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.chat-bubble.out .chat-avatar {
  background: linear-gradient(135deg, var(--green), #16a34a);
}

.bubble-text {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 16px 16px 16px 4px;
  padding: 10px 16px;
  font-size: 13px;
  color: var(--text2);
  max-width: 220px;
  line-height: 1.5;
}

.chat-bubble.out .bubble-text {
  background: linear-gradient(135deg, rgba(0,194,255,0.15), rgba(37,99,235,0.15));
  border-color: rgba(0,194,255,0.25);
  border-radius: 16px 16px 4px 16px;
  color: var(--text);
}

.enc-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 6px;
  padding: 10px;
  background: rgba(0,194,255,0.05);
  border: 1px solid rgba(0,194,255,0.12);
  border-radius: 12px;
  font-family: 'Orbitron', monospace;
  font-size: 10px;
  color: rgba(0,194,255,0.6);
  letter-spacing: 1.5px;
}

.stats-row {
  display: flex;
  gap: 12px;
  margin-top: 16px;
}

.stat-chip {
  flex: 1;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 14px;
  padding: 14px 10px;
  text-align: center;
}

.stat-num {
  font-family: 'Orbitron', monospace;
  font-size: 20px;
  font-weight: 900;
  color: var(--text);
}

.stat-lbl {
  font-size: 10px;
  color: var(--text3);
  margin-top: 2px;
  letter-spacing: 0.5px;
}

.stat-chip.cyan .stat-num { color: var(--accent); }
.stat-chip.green .stat-num { color: var(--green); }

/* Footer */
.footer {
  text-align: center;
  padding: 20px;
  font-size: 12px;
  color: var(--text3);
  position: relative;
  z-index: 1;
}

@media (max-width: 900px) {
  .hero-content { grid-template-columns: 1fr; }
  .hero-card { display: none; }
}
</style>

<!-- NAVBAR -->
<nav class="navbar">
  <a href="/" class="logo">
    <div class="logo-icon">🔒</div>
    <div class="logo-text">
      <div class="name">ENY-CHAT</div>
      <div class="tagline">Secure Messenger</div>
    </div>
  </a>
  <a href="auth/auth.php?auth=login" class="nav-login">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
    </svg>
    LOGIN
  </a>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="hero-glow-left"></div>
  <div class="hero-glow-right"></div>

  <div class="hero-content">

    <!-- Left -->
    <div>
      <div class="hero-eyebrow">LIVE &amp; ENCRYPTED</div>

      <h1 class="hero-title">
        Next-Gen
        <span class="accent-word">SECURE CHAT</span>
        Platform
      </h1>

      <p class="hero-desc">
        Send messages with military-grade AES-256 encryption, real-time delivery, and advanced authentication — built for privacy-first communication.
      </p>

      <div class="badges">
        <span class="badge badge-cyan">🔒 End-to-End Encrypted</span>
        <span class="badge badge-green">⚡ Instant Delivery</span>
        <span class="badge badge-purple">🛡️ Anonymous Access</span>
      </div>

      <!-- ADVANCED START SECURE CHAT BUTTON -->
      <div class="cta-wrap">
        <div style="position:relative; display:inline-block;">
          <div class="btn-glow-ring"></div>
          <a href="auth/auth.php?auth=login" class="btn-start-chat">
            <span class="ripple"></span>
            <div class="btn-icon">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <div class="btn-label">
              <span class="main">START SECURE CHAT</span>
              <span class="sub">AES-256 Protected</span>
            </div>
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5" style="opacity:0.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
          </a>
        </div>

        <a href="auth/auth.php?auth=signup" class="btn-secondary">
          Create Account
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </a>
      </div>
    </div>

    <!-- Right Glass Card -->
    <div class="hero-card">
      <div class="card-glow"></div>

      <div class="chat-preview">
        <div class="chat-bubble" style="animation-delay:0s">
          <div class="chat-avatar">👤</div>
          <div class="bubble-text">Hey! Is this chat really encrypted? 🔐</div>
        </div>
        <div class="chat-bubble out" style="animation-delay:0.15s">
          <div class="chat-avatar">🔒</div>
          <div class="bubble-text">100% — AES-256 end-to-end. No one can read this.</div>
        </div>
        <div class="chat-bubble" style="animation-delay:0.3s">
          <div class="chat-avatar">👤</div>
          <div class="bubble-text">Incredible. This is the future of messaging ⚡</div>
        </div>
      </div>

      <div class="enc-badge">
        <svg width="11" height="11" fill="rgba(0,194,255,0.6)" viewBox="0 0 24 24"><path d="M12 1l9 4v6c0 5.25-3.75 10.15-9 11.5C6.75 21.15 3 16.25 3 11V5l9-4z"/></svg>
        AES-256 ENCRYPTED CHANNEL
      </div>

      <div class="stats-row">
        <div class="stat-chip cyan">
          <div class="stat-num">256</div>
          <div class="stat-lbl">BIT KEY</div>
        </div>
        <div class="stat-chip green">
          <div class="stat-num">0ms</div>
          <div class="stat-lbl">LATENCY</div>
        </div>
        <div class="stat-chip">
          <div class="stat-num">∞</div>
          <div class="stat-lbl">PRIVATE</div>
        </div>
      </div>
    </div>

  </div>
</div>

<p class="footer">Developed by Mahfuj Ansari</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.4/flowbite.min.js"></script>
</body>
</html>
