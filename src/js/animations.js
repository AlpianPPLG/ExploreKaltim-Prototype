/**
 * ========================================
 * EXPLORE KALTIM - Animations
 * ========================================
 * Logic untuk AOS dan animasi custom
 */

// Initialize AOS (Animate On Scroll)
function initAOS() {
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      easing: 'ease-out-cubic',
      once: true,
      offset: 50,
      delay: 0,
      anchorPlacement: 'top-bottom'
    });
  }
}

// Parallax effect for background elements
function initParallax() {
  const parallaxElements = document.querySelectorAll('[data-parallax]');

  if (parallaxElements.length === 0) return;

  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;

    parallaxElements.forEach(element => {
      const speed = element.dataset.parallax || 0.5;
      const yPos = -(scrolled * speed);
      element.style.transform = `translateY(${yPos}px)`;
    });
  }, { passive: true });
}

// Counter animation for stats
function animateCounter(element, target, duration = 2000) {
  const start = 0;
  const increment = target / (duration / 16);
  let current = start;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      element.textContent = target.toLocaleString();
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current).toLocaleString();
    }
  }, 16);
}

// Initialize counter animations when elements are in view
function initCounterAnimations() {
  const counters = document.querySelectorAll('[data-counter]');

  if (counters.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const target = parseInt(entry.target.dataset.counter);
        animateCounter(entry.target, target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(counter => observer.observe(counter));
}

// Smooth reveal animation for elements
function initRevealAnimations() {
  const revealElements = document.querySelectorAll('[data-reveal]');

  if (revealElements.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  revealElements.forEach(el => observer.observe(el));
}

// Staggered animation for grid items
function initStaggeredAnimations() {
  const staggerContainers = document.querySelectorAll('[data-stagger]');

  staggerContainers.forEach(container => {
    const items = container.children;
    Array.from(items).forEach((item, index) => {
      item.style.transitionDelay = `${index * 100}ms`;
    });
  });
}

// Magnetic button effect
function initMagneticButtons() {
  const magneticButtons = document.querySelectorAll('[data-magnetic]');

  magneticButtons.forEach(button => {
    button.addEventListener('mousemove', (e) => {
      const rect = button.getBoundingClientRect();
      const x = e.clientX - rect.left - rect.width / 2;
      const y = e.clientY - rect.top - rect.height / 2;

      button.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
    });

    button.addEventListener('mouseleave', () => {
      button.style.transform = 'translate(0, 0)';
    });
  });
}

// Text typing animation
function typeText(element, text, speed = 50) {
  return new Promise(resolve => {
    let index = 0;
    element.textContent = '';

    const timer = setInterval(() => {
      if (index < text.length) {
        element.textContent += text.charAt(index);
        index++;
      } else {
        clearInterval(timer);
        resolve();
      }
    }, speed);
  });
}

// Initialize all animations
function initAnimations() {
  initAOS();
  initParallax();
  initCounterAnimations();
  initRevealAnimations();
  initStaggeredAnimations();
  initMagneticButtons();
}

// Export functions
if (typeof window !== 'undefined') {
  window.initAnimations = initAnimations;
  window.animateCounter = animateCounter;
  window.typeText = typeText;
}
