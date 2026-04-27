<style>
  .auth-card {
    background: linear-gradient(135deg, rgba(15,23,42,0.95) 0%, rgba(17,24,39,0.98) 100%);
    border: 1px solid rgba(6,182,212,0.15);
    backdrop-filter: blur(12px);
  }
  .auth-input {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: #e2e8f0 !important;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .auth-input:focus {
    border-color: rgba(6,182,212,0.6) !important;
    box-shadow: 0 0 0 3px rgba(6,182,212,0.1) !important;
    outline: none !important;
  }
  .auth-input::placeholder { color: #4b5563; }
  .btn-primary {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    transition: all 0.2s;
  }
  .btn-primary:hover {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 20px rgba(6,182,212,0.3);
  }
  .user-icon { animation: float 3s ease-in-out infinite; }
  @keyframes float {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-6px); }
  }
</style>

<div class="min-h-screen flex items-center justify-center px-4 py-12" style="background: radial-gradient(ellipse at 50% 0%, rgba(6,182,212,0.08) 0%, transparent 60%)">
  <div class="auth-card w-full max-w-md rounded-2xl p-8 shadow-2xl">

    <!-- Icon -->
    <div class="text-center mb-8">
      <div class="user-icon inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background:linear-gradient(135deg,#0891b2,#0e7490)">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white mb-1">Create Account</h1>
      <p class="text-gray-500 text-sm">Join the secure chat platform</p>
    </div>

    <form action="/api/signup.php" method="POST" enctype="multipart/form-data" autocomplete="on" class="space-y-5">

      <!-- Username -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">Username</label>
        <div class="relative">
          <input type="text" name="username" onkeyup="nospaces(this)" maxlength="30" required
            class="auth-input w-full rounded-xl p-4 pr-12 text-sm"
            placeholder="Choose a username" />
          <span class="absolute inset-y-0 right-4 flex items-center text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">Email Address</label>
        <div class="relative">
          <input type="email" name="email" required
            class="auth-input w-full rounded-xl p-4 pr-12 text-sm"
            placeholder="you@example.com" />
          <span class="absolute inset-y-0 right-4 flex items-center text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">Password</label>
        <div class="relative">
          <input type="password" name="password" id="pass1" required
            class="auth-input w-full rounded-xl p-4 pr-12 text-sm"
            placeholder="Create a strong password" />
          <span class="absolute inset-y-0 right-4 flex items-center text-gray-500 cursor-pointer" onclick="showPass()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">Confirm Password</label>
        <div class="relative">
          <input type="password" name="c_password" id="pass2" required
            class="auth-input w-full rounded-xl p-4 pr-12 text-sm"
            placeholder="Repeat your password" />
          <span class="absolute inset-y-0 right-4 flex items-center text-gray-500 cursor-pointer" onclick="showPass()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Security notice -->
      <div class="flex items-center gap-2 px-3 py-2 rounded-lg" style="background:rgba(6,182,212,0.05);border:1px solid rgba(6,182,212,0.1)">
        <svg class="w-4 h-4 text-cyan-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="text-xs text-cyan-600">Your password is hashed with bcrypt · Messages encrypted with AES-256</span>
      </div>

      <button type="submit" class="btn-primary w-full rounded-xl py-3.5 text-sm font-semibold text-white">
        Create Account
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
      Already have an account? 
      <a href="auth.php?auth=login" class="text-cyan-400 hover:text-cyan-300 font-medium">Sign in</a>
    </p>

    <p class="text-center text-xs text-gray-700 mt-6">
      Developed by <span class="text-gray-500 font-medium">Mahfuj Ansari</span>
    </p>
  </div>
</div>
