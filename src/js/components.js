/**
 * ========================================
 * EXPLORE KALTIM - Components Loader
 * ========================================
 * Script untuk memuat komponen HTML secara modular
 */

// Component configuration
const componentConfig = [
  { id: 'navbar-placeholder', path: './src/components/navbar.html' },
  { id: 'hero-placeholder', path: './src/components/hero.html' },
  { id: 'destinations-placeholder', path: './src/components/destinations.html' },
  { id: 'experience-placeholder', path: './src/components/experience.html' },
  { id: 'gallery-placeholder', path: './src/components/gallery.html' },
  { id: 'testimonials-placeholder', path: './src/components/testimonials.html' },
  { id: 'contact-placeholder', path: './src/components/contact.html' },
  { id: 'footer-placeholder', path: './src/components/footer.html' }
];

// Load a single component
async function loadComponent(id, path) {
  try {
    const element = document.getElementById(id);
    if (!element) {
      console.warn(`Element with id "${id}" not found`);
      return null;
    }

    const response = await fetch(path);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const html = await response.text();
    element.innerHTML = html;

    // Dispatch custom event when component is loaded
    document.dispatchEvent(new CustomEvent('componentLoaded', {
      detail: { id, path }
    }));

    return html;
  } catch (error) {
    console.error(`Error loading component ${path}:`, error);
    return null;
  }
}

// Load all components sequentially (to maintain order)
async function loadAllComponents() {
  let loadedCount = 0;
  const totalComponents = componentConfig.length;

  for (const { id, path } of componentConfig) {
    await loadComponent(id, path);
    loadedCount++;

    // Update progress (optional - can be used for loader)
    const progress = (loadedCount / totalComponents) * 100;
    updateLoaderProgress(progress);
  }

  // Dispatch event when all components are loaded
  document.dispatchEvent(new CustomEvent('allComponentsLoaded'));
  return true;
}

// Update loader progress bar
function updateLoaderProgress(progress) {
  const progressBar = document.querySelector('.loader-progress-bar');
  if (progressBar) {
    progressBar.style.width = `${progress}%`;
    progressBar.style.animation = 'none';
  }
}

// Export functions for global access
window.loadComponent = loadComponent;
window.loadAllComponents = loadAllComponents;
window.componentConfig = componentConfig;

