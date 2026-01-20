/**
 * ========================================
 * EXPLORE KALTIM - Main JavaScript
 * ========================================
 * Entry point untuk semua fungsionalitas website
 */

// ==========================================
// DOM Ready Handler
// ==========================================
document.addEventListener("DOMContentLoaded", async () => {
  console.log("🚀 Initializing Explore Kaltim...");

  try {
    // Load all components first
    await loadAllComponents();

    // Initialize all modules after components are loaded
    initializeApp();

    // Hide loader with delay for smooth transition
    setTimeout(() => hideLoader(), 800);

    console.log("✅ Explore Kaltim initialized successfully!");
  } catch (error) {
    console.error("❌ Error initializing app:", error);
    // Hide loader even on error
    hideLoader();
  }
});

// ==========================================
// Initialize All App Modules
// ==========================================
function initializeApp() {
  initNavbar();
  initMobileMenu();
  initVideoModal();
  initDestinations();
  initFilters();
  initGallery();
  initTestimonials();
  initContactForm();
  initBackToTop();
  initSmoothScroll();
  initAnimations();
}

// ==========================================
// Page Loader
// ==========================================
function hideLoader() {
  const loader = document.getElementById("page-loader");
  if (loader) {
    loader.classList.add("loaded");
    setTimeout(() => {
      loader.style.display = "none";
    }, 600);
  }
}

// ==========================================
// Navbar Functionality
// ==========================================
function initNavbar() {
  const navbar = document.getElementById("navbar");
  if (!navbar) return;

  let lastScroll = 0;
  let ticking = false;

  window.addEventListener(
    "scroll",
    () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          const currentScroll = window.pageYOffset;

          // Add background on scroll
          if (currentScroll > 50) {
            navbar.classList.add("scrolled");
          } else {
            navbar.classList.remove("scrolled");
          }

          // Hide/show navbar on scroll direction (only after 500px)
          if (currentScroll > 500) {
            if (currentScroll > lastScroll) {
              navbar.style.transform = "translateY(-100%)";
            } else {
              navbar.style.transform = "translateY(0)";
            }
          } else {
            navbar.style.transform = "translateY(0)";
          }

          lastScroll = currentScroll;
          ticking = false;
        });
        ticking = true;
      }
    },
    { passive: true },
  );
}

// ==========================================
// Mobile Menu
// ==========================================
function initMobileMenu() {
  const menuBtn = document.getElementById("mobile-menu-btn");
  const mobileMenu = document.getElementById("mobile-menu");

  if (!menuBtn || !mobileMenu) return;

  let isOpen = false;

  menuBtn.addEventListener("click", () => {
    isOpen = !isOpen;
    toggleMobileMenu(isOpen, menuBtn, mobileMenu);
  });

  // Close menu when clicking links
  const menuLinks = mobileMenu.querySelectorAll("[data-close-menu]");
  menuLinks.forEach((link) => {
    link.addEventListener("click", () => {
      isOpen = false;
      toggleMobileMenu(isOpen, menuBtn, mobileMenu);
    });
  });
}

function toggleMobileMenu(isOpen, menuBtn, mobileMenu) {
  const lines = menuBtn.querySelectorAll(".hamburger-line");
  const isMobile = window.innerWidth <= 480;
  const translateY = isMobile ? "6px" : "8px";
  const lineWidth = isMobile ? "20px" : "24px";

  if (isOpen) {
    mobileMenu.classList.add("open");
    document.body.style.overflow = "hidden";

    // Animate hamburger to X - centered and properly aligned
    lines[0].style.transform = `translateY(${translateY}) rotate(45deg)`;
    lines[0].style.width = lineWidth;
    lines[1].style.opacity = "0";
    lines[1].style.transform = "scaleX(0)";
    lines[2].style.transform = `translateY(-${translateY}) rotate(-45deg)`;
    lines[2].style.width = lineWidth;
    lines[2].style.marginLeft = "0";
  } else {
    mobileMenu.classList.remove("open");
    document.body.style.overflow = "";

    // Reset hamburger
    lines[0].style.transform = "";
    lines[0].style.width = "";
    lines[1].style.opacity = "1";
    lines[1].style.transform = "";
    lines[2].style.transform = "";
    lines[2].style.width = "";
    lines[2].style.marginLeft = "";
  }
}

// ==========================================
// Video Modal
// ==========================================
function initVideoModal() {
  const playBtn = document.getElementById("play-video-btn");
  const modal = document.getElementById("video-modal");
  const closeBtn = document.getElementById("close-video-modal");
  const youtubePlayer = document.getElementById("youtube-player");

  if (!playBtn || !modal) return;

  // Sample video URL (replace with actual Kalimantan Timur tourism video)
  const videoUrl = "https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1";

  playBtn.addEventListener("click", () => {
    modal.classList.add("active");
    if (youtubePlayer) {
      youtubePlayer.src = videoUrl;
    }
    document.body.style.overflow = "hidden";
  });

  function closeModal() {
    modal.classList.remove("active");
    if (youtubePlayer) {
      youtubePlayer.src = "";
    }
    document.body.style.overflow = "";
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", closeModal);
  }

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("active")) {
      closeModal();
    }
  });
}

// ==========================================
// Destinations Section
// ==========================================
function initDestinations() {
  const container = document.getElementById("destinations-grid");
  if (!container || typeof destinations === "undefined") return;

  renderDestinations(destinations);
}

function renderDestinations(items, animate = true) {
  const container = document.getElementById("destinations-grid");
  if (!container) return;

  container.innerHTML = items
    .map(
      (dest, index) => `
    <article class="destination-card group" data-aos="${animate ? "fade-up" : ""}" data-aos-delay="${index * 100}" data-category="${dest.category}">
      <div class="relative overflow-hidden">
        <img 
          src="${dest.image}" 
          alt="${dest.name}" 
          class="card-image"
          loading="lazy"
        >
        <div class="card-overlay"></div>
        
        <!-- Category Badge -->
        <div class="absolute top-4 left-4 z-10">
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-sm text-xs font-display font-semibold text-primary-800">
            ${getCategoryIcon(dest.category)}
            ${getCategoryName(dest.category)}
          </span>
        </div>

        <!-- Rating Badge -->
        <div class="absolute top-4 right-4 z-10">
          <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-full bg-black/50 backdrop-blur-sm text-white text-sm font-medium">
            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            ${dest.rating}
          </span>
        </div>

        <!-- Quick Info Overlay -->
        <div class="absolute bottom-0 left-0 right-0 p-6 z-10 transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
          <div class="flex items-center gap-4 text-white text-sm">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              ${dest.duration}
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              ${dest.reviews.toLocaleString()} reviews
            </span>
          </div>
        </div>
      </div>

      <div class="p-6">
        <div class="flex items-start justify-between gap-4 mb-3">
          <h3 class="text-xl font-heading font-bold text-gray-900 group-hover:text-primary-700 transition-colors">
            ${dest.name}
          </h3>
        </div>
        
        <p class="flex items-center gap-1.5 text-sm text-gray-500 mb-3">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
          ${dest.location}
        </p>

        <p class="text-gray-600 text-sm line-clamp-2 mb-4">
          ${dest.shortDesc}
        </p>

        <!-- Highlights -->
        <div class="flex flex-wrap gap-2 mb-5">
          ${dest.highlights
            .slice(0, 3)
            .map(
              (h) => `
            <span class="px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-medium">
              ${h}
            </span>
          `,
            )
            .join("")}
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
          <div>
            <span class="text-xs text-gray-500">Mulai dari</span>
            <p class="text-lg font-heading font-bold text-primary-800">${dest.price}</p>
          </div>
          <a href="#contact" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-800 text-white text-sm font-display font-medium hover:bg-primary-700 transition-colors">
            Lihat Detail
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </div>
      </div>
    </article>
  `,
    )
    .join("");

  // Refresh AOS after rendering
  if (typeof AOS !== "undefined") {
    AOS.refresh();
  }
}

function getCategoryIcon(category) {
  const icons = {
    water: "🌊",
    forest: "🌲",
    culture: "🏛️",
  };
  return icons[category] || "🌏";
}

function getCategoryName(category) {
  const names = {
    water: "Wisata Air",
    forest: "Hutan & Alam",
    culture: "Budaya",
  };
  return names[category] || "Destinasi";
}

// ==========================================
// Category Filters
// ==========================================
function initFilters() {
  const filterContainer = document.getElementById("filter-container");
  if (!filterContainer || typeof categories === "undefined") return;

  // Render filter pills
  filterContainer.innerHTML = categories
    .map(
      (cat) => `
    <button 
      class="filter-pill ${cat.id === "all" ? "active" : ""}" 
      data-filter="${cat.id}"
    >
      <span class="mr-1">${cat.icon}</span>
      ${cat.name}
    </button>
  `,
    )
    .join("");

  // Add filter functionality
  const filterBtns = filterContainer.querySelectorAll(".filter-pill");

  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      // Update active state
      filterBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      // Filter destinations
      const filter = btn.dataset.filter;
      const filtered =
        filter === "all"
          ? destinations
          : destinations.filter((d) => d.category === filter);

      renderDestinations(filtered, false);
    });
  });
}

// ==========================================
// Gallery Section
// ==========================================
function initGallery() {
  const galleryContainer = document.getElementById("gallery-grid");
  if (!galleryContainer || typeof galleryImages === "undefined") return;

  // Render gallery
  galleryContainer.innerHTML = galleryImages
    .map(
      (img, index) => `
    <div 
      class="gallery-item aspect-square"
      data-aos="zoom-in"
      data-aos-delay="${index * 50}"
      onclick="openLightbox(${img.id})"
    >
      <img 
        src="${img.src}" 
        alt="${img.alt}" 
        loading="lazy"
      >
      <div class="gallery-overlay">
        <p class="text-white font-display font-medium text-sm">${img.alt}</p>
      </div>
    </div>
  `,
    )
    .join("");
}

// ==========================================
// Lightbox Functionality
// ==========================================
let currentLightboxIndex = 0;

function openLightbox(id) {
  const lightbox = document.getElementById("lightbox");
  const lightboxImage = document.getElementById("lightbox-image");

  if (!lightbox || !lightboxImage || typeof galleryImages === "undefined")
    return;

  const imageIndex = galleryImages.findIndex((img) => img.id === id);
  if (imageIndex === -1) return;

  currentLightboxIndex = imageIndex;
  const image = galleryImages[currentLightboxIndex];

  lightboxImage.src = image.src;
  lightboxImage.alt = image.alt;

  lightbox.classList.add("active");
  document.body.style.overflow = "hidden";
}

function closeLightbox() {
  const lightbox = document.getElementById("lightbox");
  if (!lightbox) return;

  lightbox.classList.remove("active");
  document.body.style.overflow = "";
}

function navigateLightbox(direction) {
  const lightboxImage = document.getElementById("lightbox-image");
  if (!lightboxImage || typeof galleryImages === "undefined") return;

  currentLightboxIndex += direction;

  if (currentLightboxIndex < 0) {
    currentLightboxIndex = galleryImages.length - 1;
  } else if (currentLightboxIndex >= galleryImages.length) {
    currentLightboxIndex = 0;
  }

  const image = galleryImages[currentLightboxIndex];
  lightboxImage.src = image.src;
  lightboxImage.alt = image.alt;
}

// Close lightbox with Escape key
document.addEventListener("keydown", (e) => {
  const lightbox = document.getElementById("lightbox");
  if (!lightbox) return;

  if (e.key === "Escape" && lightbox.classList.contains("active")) {
    closeLightbox();
  } else if (e.key === "ArrowLeft" && lightbox.classList.contains("active")) {
    navigateLightbox(-1);
  } else if (e.key === "ArrowRight" && lightbox.classList.contains("active")) {
    navigateLightbox(1);
  }
});

// Make functions global
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.navigateLightbox = navigateLightbox;

// ==========================================
// Testimonials Section
// ==========================================
function initTestimonials() {
  const container = document.getElementById("testimonials-container");
  if (!container || typeof testimonials === "undefined") return;

  container.innerHTML = testimonials
    .map(
      (t, index) => `
    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="${index * 100}">
      <div class="flex items-center gap-1 mb-4">
        ${Array(t.rating)
          .fill()
          .map(
            () => `
          <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        `,
          )
          .join("")}
      </div>
      
      <p class="text-gray-700 mb-6 leading-relaxed italic line-clamp-3">
        "${t.text}"
      </p>
      
      <div class="flex items-center gap-4">
        <img 
          src="${t.avatar}" 
          alt="${t.name}" 
          class="w-14 h-14 rounded-full object-cover ring-2 ring-primary-100"
          loading="lazy"
        >
        <div>
          <h4 class="font-display font-semibold text-gray-900">${t.name}</h4>
          <p class="text-sm text-gray-500">${t.role} • ${t.country}</p>
          <p class="text-xs text-primary-600 mt-0.5">${t.destination}</p>
        </div>
      </div>
    </div>
  `,
    )
    .join("");
}

// ==========================================
// Contact Form
// ==========================================
function initContactForm() {
  const form = document.getElementById("contact-form");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span>Mengirim...</span>
    `;

    // Simulate form submission
    await new Promise((resolve) => setTimeout(resolve, 2000));

    // Show success message
    submitBtn.innerHTML = `
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>Terkirim!</span>
    `;
    submitBtn.classList.remove("bg-primary-800");
    submitBtn.style.background = "linear-gradient(135deg, #059669, #10b981)";

    // Reset form
    form.reset();

    // Reset button after delay
    setTimeout(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      submitBtn.style.background = "";
    }, 3000);
  });
}

// ==========================================
// Back to Top Button
// ==========================================
function initBackToTop() {
  const backToTopBtn = document.getElementById("back-to-top");
  if (!backToTopBtn) return;

  window.addEventListener(
    "scroll",
    () => {
      if (window.pageYOffset > 500) {
        backToTopBtn.classList.add("visible");
      } else {
        backToTopBtn.classList.remove("visible");
      }
    },
    { passive: true },
  );

  backToTopBtn.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
}

// ==========================================
// Smooth Scroll for Anchor Links
// ==========================================
function initSmoothScroll() {
  document.addEventListener("click", (e) => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;

    const targetId = link.getAttribute("href");
    if (targetId === "#") return;

    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      e.preventDefault();
      targetElement.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
}
