/**
 * ========================================
 * EXPLORE KALTIM - Components Loader
 * ========================================
 * Script untuk memuat komponen HTML secara modular
 */

// Component loader function
async function loadComponent(id, path) {
  try {
    const response = await fetch(path);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const html = await response.text();
    const element = document.getElementById(id);
    if (element) {
      element.innerHTML = html;
      // Dispatch custom event when component is loaded
      document.dispatchEvent(new CustomEvent('componentLoaded', {
        detail: { id, path }
      }));
    }
    return html;
  } catch (error) {
    console.error(`Error loading component ${path}:`, error);
    return null;
  }
}

// Load multiple components in parallel
async function loadComponents(components) {
  const promises = components.map(({ id, path }) => loadComponent(id, path));
  return Promise.all(promises);
}

// Initialize all page components
async function initializeComponents() {
  const components = [
    { id: 'navbar-placeholder', path: './src/components/navbar.html' },
    { id: 'hero-placeholder', path: './src/components/hero.html' },
    { id: 'footer-placeholder', path: './src/components/footer.html' }
  ];

  // Filter to only load components that have existing placeholders
  const existingComponents = components.filter(({ id }) => document.getElementById(id) !== null);

  if (existingComponents.length > 0) {
    await loadComponents(existingComponents);
  }

  // Dispatch event when all components are loaded
  document.dispatchEvent(new CustomEvent('allComponentsLoaded'));
}

// Export functions for use in other scripts
if (typeof window !== 'undefined') {
  window.loadComponent = loadComponent;
  window.loadComponents = loadComponents;
  window.initializeComponents = initializeComponents;
}
