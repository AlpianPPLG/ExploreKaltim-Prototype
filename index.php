<?php
// Start session
session_start();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Jelajahi keajaiban Kalimantan Timur - Kepulauan Derawan, Labuan Cermin, Bukit Bangkirai dan destinasi wisata eksotis lainnya di Borneo.">
  <meta name="keywords" content="Kalimantan Timur, Derawan, Labuan Cermin, wisata Borneo, diving Indonesia, ekowisata">
  <meta name="author" content="Explore Kaltim">

  <!-- Open Graph / Social Media -->
  <meta property="og:title" content="Explore Kaltim - Jelajahi Keajaiban Kalimantan Timur">
  <meta property="og:description" content="Temukan surga tersembunyi di Borneo - dari kepulauan Derawan yang memukau hingga hutan hujan purba yang misterius">
  <meta property="og:image" content="https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=1200&q=80">
  <meta property="og:type" content="website">

  <title>Explore Kaltim - Jelajahi Keajaiban Kalimantan Timur</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="./src/assets/icons/favicon.svg">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- AOS - Animate On Scroll -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#ecfdf5',
              100: '#d1fae5',
              200: '#a7f3d0',
              300: '#6ee7b7',
              400: '#34d399',
              500: '#10b981',
              600: '#059669',
              700: '#047857',
              800: '#065f46',
              900: '#064e3b',
              950: '#022c22',
            },
            secondary: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              300: '#7dd3fc',
              400: '#38bdf8',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              800: '#075985',
              900: '#0c4a6e',
              950: '#082f49',
            },
            accent: {
              50: '#fafaf9',
              100: '#f5f5f4',
              200: '#e7e5e4',
              300: '#d6d3d1',
            },
          },
          fontFamily: {
            'heading': ['Playfair Display', 'serif'],
            'display': ['Montserrat', 'sans-serif'],
            'body': ['Poppins', 'sans-serif'],
          },
        },
      },
    }
  </script>

  <!-- Custom Styles -->
  <link rel="stylesheet" href="./src/css/style.css">
</head>

<body class="bg-accent-100 text-gray-800 antialiased font-body">

  <!-- ==================== PAGE LOADER ==================== -->
  <div id="page-loader">
    <div class="loader-content">
      <div class="loader-spinner"></div>
      <p class="loader-text">Loading paradise...</p>
      <div class="loader-progress">
        <div class="loader-progress-bar"></div>
      </div>
    </div>
  </div>

  <!-- ==================== NAVBAR ==================== -->
  <?php include 'navbar.php'; ?>

  <!-- ==================== HERO PLACEHOLDER ==================== -->
  <div id="hero-placeholder"></div>

  <!-- ==================== MAIN CONTENT ==================== -->
  <main>
    <!-- Destinations Section -->
    <section id="destinations-placeholder"></section>

    <!-- Experience/Stats Section -->
    <section id="experience-placeholder"></section>

    <!-- Gallery Section -->
    <section id="gallery-placeholder"></section>

    <!-- Testimonials Section -->
    <section id="testimonials-placeholder"></section>

    <!-- Contact Section -->
    <section id="contact-placeholder"></section>

    <!-- CTA Section -->
    <section id="cta-placeholder"></section>
  </main>

  <!-- ==================== FOOTER PLACEHOLDER ==================== -->
  <div id="footer-placeholder"></div>

  <!-- ==================== BACK TO TOP BUTTON ==================== -->
  <button id="back-to-top" class="back-to-top" aria-label="Kembali ke atas">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
    </svg>
  </button>

  <!-- ==================== LIGHTBOX MODAL ==================== -->
  <div id="lightbox" class="lightbox">
    <button onclick="closeLightbox()" class="lightbox-close" aria-label="Close">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <button onclick="navigateLightbox(-1)" class="lightbox-nav lightbox-prev" aria-label="Previous">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </button>
    <button onclick="navigateLightbox(1)" class="lightbox-nav lightbox-next" aria-label="Next">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </button>
    <img id="lightbox-image" src="" alt="" class="lightbox-image">
  </div>

  <!-- ==================== VIDEO MODAL ==================== -->
  <div id="video-modal" class="video-modal">
    <button id="close-video-modal" class="video-modal-close" aria-label="Close">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
    <div class="video-modal-content">
      <iframe id="youtube-player" src="" title="Explore Kaltim Video" frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
      </iframe>
    </div>
  </div>

  <!-- ==================== SCRIPTS ==================== -->
  <!-- AOS Library -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- App Scripts (Load order matters!) -->
  <script src="./src/js/data.js"></script>
  <script src="./src/js/components.js"></script>
  <script src="./src/js/animations.js"></script>
  <script src="./src/js/main.js"></script>
</body>
</html>

