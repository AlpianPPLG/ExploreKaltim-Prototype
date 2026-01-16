# 🌴 Explore Kaltim - East Borneo Tourism Landing Page

![Explore Kaltim](https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=1200&q=80)

A modern, responsive, and immersive landing page showcasing the natural wonders of East Kalimantan (Borneo), Indonesia.

## ✨ Features

- **🎬 Hero Section** - Stunning video background with animated elements
- **🗺️ Destination Cards** - Interactive grid with hover effects
- **🏷️ Category Filters** - Water activities, Forest/Nature, Culture
- **📸 Interactive Gallery** - Lightbox for fullscreen image viewing
- **💬 Testimonials** - Slider with visitor reviews
- **📧 Contact Form** - Simple inquiry/booking form
- **📱 Fully Responsive** - Works on all devices
- **🎨 Modern UI/UX** - Glassmorphism, parallax, smooth animations

## 🛠️ Tech Stack

- **HTML5** - Semantic markup
- **Tailwind CSS** - Utility-first CSS framework
- **Vanilla JavaScript** - No framework dependencies
- **AOS** - Animate On Scroll library

## 🎨 Design System

### Color Palette
- **Primary (Emerald Green)**: `#064e3b` - Representing rainforests
- **Secondary (Turquoise Blue)**: `#0ea5e9` - Representing crystal clear waters
- **Accent (Sandy Beige)**: `#f5f5f4` - Neutral backgrounds

### Typography
- **Headings**: Playfair Display (Elegant/Luxury feel)
- **Display**: Montserrat (Modern/Clean)
- **Body**: Poppins (Readable/Friendly)

## 📁 Project Structure

```
/explore-kaltim
├── index.html              # Main page
├── package.json            # NPM configuration
├── tailwind.config.js      # Tailwind configuration
├── .gitignore
├── README.md
│
├── /dist
│   └── /css
│       └── output.css      # Compiled Tailwind CSS
│
└── /src
    ├── /css
    │   └── style.css       # Custom styles & Tailwind directives
    │
    ├── /js
    │   ├── main.js         # Entry point
    │   ├── animations.js   # AOS/GSAP animations
    │   ├── components.js   # HTML component loader
    │   └── data.js         # Destination data
    │
    ├── /components
    │   ├── navbar.html     # Navigation
    │   ├── hero.html       # Hero section
    │   ├── card-item.html  # Card template
    │   └── footer.html     # Footer
    │
    └── /assets
        ├── /img            # Images
        ├── /video          # Video files
        └── /icons          # SVG icons
```

## 🚀 Getting Started

### Prerequisites
- Node.js 16+ 
- npm or yarn

### Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/explore-kaltim.git
cd explore-kaltim
```

2. Install dependencies
```bash
npm install
```

3. Build CSS
```bash
npm run build:css
```

4. For development (watch mode)
```bash
npm run dev
```

5. Open `index.html` in your browser or use a local server

### Using Local Server (Recommended)
```bash
# Using Python
python -m http.server 8000

# Using PHP
php -S localhost:8000

# Using Node.js (npx)
npx serve
```

## 📍 Featured Destinations

1. **Kepulauan Derawan** - World-class diving with green turtles
2. **Danau Labuan Cermin** - Crystal clear two-layer lake
3. **Pulau Maratua** - Pristine blue lagoon
4. **Bukit Bangkirai** - Rainforest canopy bridge
5. **Pulau Kakaban** - Stingless jellyfish lake
6. **Desa Adat Pampang** - Dayak cultural village
7. **Taman Nasional Kutai** - Orangutan wildlife sanctuary

## 🔧 Customization

### Adding New Destinations
Edit `src/js/data.js` and add to the `destinations` array:

```javascript
{
  id: 11,
  name: "Your Destination",
  slug: "your-destination",
  category: "water", // water, forest, or culture
  location: "Location, East Kalimantan",
  description: "Full description...",
  shortDesc: "Short description",
  image: "image-url.jpg",
  rating: 4.8,
  reviews: 1000,
  price: "Mulai Rp 500.000",
  duration: "2-3 Days",
  highlights: ["Feature 1", "Feature 2"],
  featured: true
}
```

### Changing Colors
Edit `tailwind.config.js` to modify the color palette:

```javascript
colors: {
  primary: {
    900: '#your-color',
  },
  secondary: {
    500: '#your-color',
  }
}
```

## 📱 Responsive Breakpoints

- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## 📄 License

This project is licensed under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Contact

- **Email**: hello@explorekaltim.id
- **WhatsApp**: +62 541 123 456
- **Instagram**: @explorekaltim

---

Made with ❤️ in Kalimantan Timur, Indonesia
