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
  .lock-icon { animation: float 3s ease-in-out infinite; }
  @keyframes float {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-6px); }
  }
</style>

<div class="min-h-screen flex items-center justify-center px-4 py-12" style="background: radial-gradient(ellipse at 50% 0%, rgba(6,182,212,0.08) 0%, transparent 60%)">
  <div class="auth-card w-full max-w-md rounded-2xl p-8 shadow-2xl">
    
    <!-- Logo / Icon -->
    <div class="text-center mb-8">
      <div class="lock-icon inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background:linear-gradient(135deg,#0891b2,#0e7490)">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white mb-1">Welcome Back</h1>
      <p class="text-gray-500 text-sm">Sign in to your secure chat</p>
    </div>

    <form action="../api/login.php" method="POST" enctype="multipart/form-data" autocomplete="on" class="space-y-5">
      
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
            placeholder="Enter your password" />
          <span class="absolute inset-y-0 right-4 flex items-center text-gray-500 cursor-pointer" onclick="showPass()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </span>
        </div>
      </div>

      <!-- Encryption notice -->
      <div class="flex items-center gap-2 px-3 py-2 rounded-lg" style="background:rgba(6,182,212,0.05);border:1px solid rgba(6,182,212,0.1)">
        <svg class="w-4 h-4 text-cyan-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-xs text-cyan-600">End-to-end encrypted · Passwords secured with bcrypt</span>
      </div>

      <button type="submit" class="btn-primary w-full rounded-xl py-3.5 text-sm font-semibold text-white">
        Sign In
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
      Don't have an account? 
      <a href="auth.php?auth=signup" class="text-cyan-400 hover:text-cyan-300 font-medium">Create one</a>
    </p>

    <p class="text-center text-xs text-gray-700 mt-6">
      Developed by <span class="text-gray-500 font-medium">Mahfuj Ansari</span>
    </p>
  </div>
</div>
