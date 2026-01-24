<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$username = $_SESSION['username'] ?? '';
$avatarUrl = $_SESSION['avatar_url'] ?? '';
?>

<!-- ==========================================
     NAVBAR COMPONENT
     Glassmorphism sticky navigation
     ========================================== -->

<nav id="navbar" class="navbar">
  <div class="container mx-auto px-3 sm:px-4 lg:px-8">
    <div class="flex items-center justify-between h-16 sm:h-20">
      <!-- Logo -->
      <a href="/ExploreKaltim/index.html" class="flex items-center gap-2 sm:gap-3 group">
        <div
          class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-secondary-400 to-primary-600 flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300 shadow-lg"
        >
          <svg
            class="w-5 h-5 sm:w-6 sm:h-6 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"
            />
          </svg>
        </div>
        <span class="text-lg sm:text-xl font-heading font-bold text-white">
          Explore<span class="text-secondary-400">Kaltim</span>
        </span>
      </a>

      <!-- Desktop Navigation -->
      <div class="hidden lg:flex items-center gap-8">
        <a href="/ExploreKaltim/index.html#home" class="nav-link">Beranda</a>
        <a href="/ExploreKaltim/explorasi.php" class="nav-link">Destinasi</a>
        <a href="/ExploreKaltim/index.html#gallery" class="nav-link">Galeri</a>
        <a href="/ExploreKaltim/index.html#testimonials" class="nav-link">Testimoni</a>
        <a href="/ExploreKaltim/index.html#contact" class="nav-link">Kontak</a>
      </div>

      <!-- Auth & CTA Buttons Desktop -->
      <div class="hidden lg:flex items-center gap-3">
        <?php if ($isLoggedIn): ?>
          <!-- User is logged in -->
          <a
            href="/ExploreKaltim/<?php echo $isAdmin ? 'admin' : 'user'; ?>/dashboard.php"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-display font-medium rounded-full hover:bg-white/10 transition-all duration-300"
          >
            <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" class="w-6 h-6 rounded-full">
            <span><?php echo htmlspecialchars($username); ?></span>
          </a>
          <a
            href="/ExploreKaltim/logout.php"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 text-white font-display font-medium rounded-full hover:bg-red-600 transition-all duration-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Logout
          </a>
        <?php else: ?>
          <!-- User is not logged in -->
          <a
            href="/ExploreKaltim/login.php"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-display font-medium rounded-full hover:bg-white/10 transition-all duration-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Login
          </a>
          <a
            href="/ExploreKaltim/register.php"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 backdrop-blur-sm text-white font-display font-medium rounded-full hover:bg-white/20 border border-white/20 transition-all duration-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Register
          </a>
          <a
            href="/ExploreKaltim/login.php"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-secondary-500 text-white font-display font-semibold rounded-full hover:bg-secondary-400 transform hover:scale-105 transition-all duration-300 shadow-lg shadow-secondary-500/30"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Booking
          </a>
        <?php endif; ?>
      </div>

      <!-- Mobile Menu Button -->
      <button
        id="mobile-menu-btn"
        class="lg:hidden w-10 h-10 sm:w-11 sm:h-11 flex flex-col items-center justify-center gap-1 sm:gap-1.5 rounded-xl bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-colors"
        aria-label="Toggle Menu"
      >
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line hamburger-line-short"></span>
      </button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="mobile-menu lg:hidden">
    <div class="container mx-auto px-4 py-6 sm:py-8">
      <div class="flex flex-col gap-2">
        <a href="/ExploreKaltim/index.html#home" class="text-2xl font-heading text-primary-800 py-4 border-b border-primary-100 hover:text-secondary-500 hover:pl-4 transition-all" data-close-menu>Beranda</a>
        <a href="/ExploreKaltim/explorasi.php" class="text-2xl font-heading text-primary-800 py-4 border-b border-primary-100 hover:text-secondary-500 hover:pl-4 transition-all" data-close-menu>Destinasi</a>
        <a href="/ExploreKaltim/index.html#gallery" class="text-2xl font-heading text-primary-800 py-4 border-b border-primary-100 hover:text-secondary-500 hover:pl-4 transition-all" data-close-menu>Galeri</a>
        <a href="/ExploreKaltim/index.html#testimonials" class="text-2xl font-heading text-primary-800 py-4 border-b border-primary-100 hover:text-secondary-500 hover:pl-4 transition-all" data-close-menu>Testimoni</a>
        <a href="/ExploreKaltim/index.html#contact" class="text-2xl font-heading text-primary-800 py-4 border-b border-primary-100 hover:text-secondary-500 hover:pl-4 transition-all" data-close-menu>Kontak</a>

        <!-- Auth Buttons Mobile -->
        <div class="mt-8 space-y-3">
          <?php if ($isLoggedIn): ?>
            <a href="/ExploreKaltim/<?php echo $isAdmin ? 'admin' : 'user'; ?>/dashboard.php" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-primary-800 text-white font-display font-semibold rounded-xl hover:bg-primary-700 transition-all" data-close-menu>
              <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" class="w-6 h-6 rounded-full">
              Dashboard
            </a>
            <a href="/ExploreKaltim/logout.php" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-red-500 text-white font-display font-semibold rounded-xl hover:bg-red-600 transition-all" data-close-menu>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              Logout
            </a>
          <?php else: ?>
            <a href="/ExploreKaltim/login.php" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-primary-800 text-white font-display font-semibold rounded-xl hover:bg-primary-700 transition-all" data-close-menu>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              Login
            </a>
            <a href="/ExploreKaltim/register.php" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-white border-2 border-primary-800 text-primary-800 font-display font-semibold rounded-xl hover:bg-primary-50 transition-all" data-close-menu>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
              </svg>
              Register
            </a>
            <a href="/ExploreKaltim/login.php" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-secondary-500 text-white font-display font-semibold rounded-xl hover:bg-secondary-400 transition-all" data-close-menu>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Booking Sekarang
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</nav>
