/**
 * ========================================
 * EXPLORE KALTIM - Destination Data
 * ========================================
 * Data destinasi wisata Kalimantan Timur
 */

const destinations = [
  {
    id: 1,
    name: "Kepulauan Derawan",
    slug: "derawan",
    category: "water",
    location: "Berau, Kalimantan Timur",
    description: "Surga tersembunyi dengan air kristal biru jernih, rumah bagi penyu hijau dan ubur-ubur tak menyengat. Salah satu destinasi diving terbaik di Indonesia.",
    shortDesc: "Surga diving dengan penyu hijau & ubur-ubur tak menyengat",
    image: "https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=1200&q=80",
      "https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&q=80",
      "https://images.unsplash.com/photo-1582967788606-a171c1080cb0?w=1200&q=80"
    ],
    rating: 4.9,
    reviews: 2847,
    price: "Mulai Rp 850.000",
    duration: "3-4 Hari",
    highlights: ["Snorkeling", "Diving", "Penyu", "Ubur-ubur"],
    featured: true
  },
  {
    id: 2,
    name: "Danau Labuan Cermin",
    slug: "labuan-cermin",
    category: "water",
    location: "Berau, Kalimantan Timur",
    description: "Danau ajaib dengan dua lapisan air - tawar di atas dan asin di bawah. Kejernihan airnya begitu sempurna hingga mendapat julukan 'Cermin'.",
    shortDesc: "Danau dua rasa dengan kejernihan luar biasa",
    image: "https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1200&q=80",
      "https://images.unsplash.com/photo-1439066615861-d1af74d74000?w=1200&q=80"
    ],
    rating: 4.8,
    reviews: 1923,
    price: "Mulai Rp 450.000",
    duration: "1 Hari",
    highlights: ["Snorkeling", "Fotografi", "Perahu Kaca"],
    featured: true
  },
  {
    id: 3,
    name: "Bukit Bangkirai",
    slug: "bukit-bangkirai",
    category: "forest",
    location: "Kutai Kartanegara, Kalimantan Timur",
    description: "Hutan hujan tropis yang megah dengan canopy bridge setinggi 30 meter. Rasakan sensasi berjalan di atas puncak pohon raksasa.",
    shortDesc: "Jembatan gantung di atas hutan hujan tropis",
    image: "https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1448375240586-882707db888b?w=1200&q=80",
      "https://images.unsplash.com/photo-1502082553048-f009c37129b9?w=1200&q=80"
    ],
    rating: 4.7,
    reviews: 1456,
    price: "Mulai Rp 150.000",
    duration: "Half Day",
    highlights: ["Canopy Bridge", "Trekking", "Flora Fauna"],
    featured: true
  },
  {
    id: 4,
    name: "Pulau Kakaban",
    slug: "kakaban",
    category: "water",
    location: "Berau, Kalimantan Timur",
    description: "Pulau unik dengan danau purba berisi ubur-ubur yang telah kehilangan kemampuan menyengat selama jutaan tahun evolusi.",
    shortDesc: "Berenang bersama jutaan ubur-ubur jinak",
    image: "https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?w=1200&q=80"
    ],
    rating: 4.9,
    reviews: 2156,
    price: "Termasuk Paket Derawan",
    duration: "1 Hari",
    highlights: ["Ubur-ubur Jinak", "Danau Purba", "Snorkeling"],
    featured: true
  },
  {
    id: 5,
    name: "Hutan Mangrove Tarakan",
    slug: "mangrove-tarakan",
    category: "forest",
    location: "Tarakan, Kalimantan Utara",
    description: "Ekosistem mangrove yang masih asri dengan keanekaragaman hayati tinggi. Jelajahi dengan perahu tradisional saat matahari terbenam.",
    shortDesc: "Ekosistem mangrove yang masih asri",
    image: "https://images.unsplash.com/photo-1569163139599-0f4517e36f51?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1569163139599-0f4517e36f51?w=1200&q=80"
    ],
    rating: 4.5,
    reviews: 876,
    price: "Mulai Rp 200.000",
    duration: "Half Day",
    highlights: ["Sunset Cruise", "Bird Watching", "Fotografi"],
    featured: false
  },
  {
    id: 6,
    name: "Desa Adat Pampang",
    slug: "pampang",
    category: "culture",
    location: "Samarinda, Kalimantan Timur",
    description: "Desa budaya suku Dayak Kenyah dengan pertunjukan tari tradisional, rumah lamin autentik, dan kerajinan tangan khas Borneo.",
    shortDesc: "Desa budaya suku Dayak Kenyah",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80"
    ],
    rating: 4.6,
    reviews: 1234,
    price: "Mulai Rp 100.000",
    duration: "Half Day",
    highlights: ["Tari Tradisional", "Rumah Lamin", "Kerajinan"],
    featured: true
  },
  {
    id: 7,
    name: "Air Terjun Tanah Merah",
    slug: "tanah-merah",
    category: "forest",
    location: "Samarinda, Kalimantan Timur",
    description: "Air terjun tersembunyi di tengah hutan dengan kolam alami yang menyegarkan. Sempurna untuk pelarian dari hiruk pikuk kota.",
    shortDesc: "Air terjun tersembunyi dengan kolam alami",
    image: "https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?w=1200&q=80"
    ],
    rating: 4.4,
    reviews: 654,
    price: "Mulai Rp 50.000",
    duration: "Half Day",
    highlights: ["Trekking", "Berenang", "Piknik"],
    featured: false
  },
  {
    id: 8,
    name: "Pulau Maratua",
    slug: "maratua",
    category: "water",
    location: "Berau, Kalimantan Timur",
    description: "Pulau terluar Indonesia dengan laguna biru menakjubkan dan terumbu karang yang masih pristine. Surga bagi penyelam sejati.",
    shortDesc: "Laguna biru dan terumbu karang pristine",
    image: "https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=1200&q=80"
    ],
    rating: 4.8,
    reviews: 1876,
    price: "Mulai Rp 1.200.000",
    duration: "4-5 Hari",
    highlights: ["Diving", "Laguna", "Resort"],
    featured: true
  },
  {
    id: 9,
    name: "Museum Mulawarman",
    slug: "museum-mulawarman",
    category: "culture",
    location: "Tenggarong, Kalimantan Timur",
    description: "Istana bersejarah Kesultanan Kutai Kartanegara dengan koleksi artefak kerajaan Hindu tertua di Indonesia.",
    shortDesc: "Warisan Kesultanan Kutai Kartanegara",
    image: "https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=1200&q=80"
    ],
    rating: 4.3,
    reviews: 432,
    price: "Rp 10.000",
    duration: "2-3 Jam",
    highlights: ["Sejarah", "Artefak", "Arsitektur"],
    featured: false
  },
  {
    id: 10,
    name: "Taman Nasional Kutai",
    slug: "tn-kutai",
    category: "forest",
    location: "Kutai Timur, Kalimantan Timur",
    description: "Salah satu taman nasional tertua di Indonesia, rumah bagi orangutan liar, beruang madu, dan beragam satwa endemik Borneo.",
    shortDesc: "Habitat orangutan & beruang madu liar",
    image: "https://images.unsplash.com/photo-1564349683136-77e08dba1ef7?w=800&q=80",
    gallery: [
      "https://images.unsplash.com/photo-1564349683136-77e08dba1ef7?w=1200&q=80"
    ],
    rating: 4.7,
    reviews: 987,
    price: "Mulai Rp 350.000",
    duration: "2-3 Hari",
    highlights: ["Orangutan", "Wildlife", "Trekking"],
    featured: true
  }
];

// Stats Data
const stats = [
  {
    number: "100+",
    label: "Spot Diving",
    icon: `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>`
  },
  {
    number: "5",
    label: "Taman Nasional",
    icon: `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
          </svg>`
  },
  {
    number: "50+",
    label: "Pulau Eksotis",
    icon: `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
          </svg>`
  },
  {
    number: "10K+",
    label: "Wisatawan/Tahun",
    icon: `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>`
  }
];

// Testimonials Data
const testimonials = [
  {
    id: 1,
    name: "Sarah Mitchell",
    role: "Travel Blogger",
    country: "Australia",
    avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&q=80",
    rating: 5,
    text: "Derawan adalah surga tersembunyi yang pernah saya kunjungi! Berenang bersama penyu hijau dan ubur-ubur tak menyengat adalah pengalaman yang tidak akan pernah saya lupakan.",
    destination: "Kepulauan Derawan"
  },
  {
    id: 2,
    name: "Budi Santoso",
    role: "Fotografer",
    country: "Indonesia",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&q=80",
    rating: 5,
    text: "Labuan Cermin adalah lokasi foto paling menakjubkan yang pernah saya temukan. Kejernihan airnya luar biasa - kamera bawah air saya menangkap warna yang sangat vivid!",
    destination: "Danau Labuan Cermin"
  },
  {
    id: 3,
    name: "Emma Thompson",
    role: "Marine Biologist",
    country: "United Kingdom",
    avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&q=80",
    rating: 5,
    text: "Sebagai ahli biologi laut, saya terkesan dengan kondisi terumbu karang di Maratua. Ini adalah salah satu ekosistem laut paling sehat yang pernah saya teliti.",
    destination: "Pulau Maratua"
  },
  {
    id: 4,
    name: "Takeshi Yamamoto",
    role: "Adventure Traveler",
    country: "Japan",
    avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&q=80",
    rating: 5,
    text: "Berjalan di jembatan kanopi Bukit Bangkirai memberikan perspektif baru tentang hutan hujan Borneo. Pemandangan dari ketinggian 30 meter sungguh menakjubkan!",
    destination: "Bukit Bangkirai"
  }
];

// Filter Categories
const categories = [
  { id: "all", name: "Semua Destinasi", icon: "🌏" },
  { id: "water", name: "Wisata Air", icon: "🌊" },
  { id: "forest", name: "Hutan & Alam", icon: "🌲" },
  { id: "culture", name: "Budaya", icon: "🏛️" }
];

// Gallery Images
const galleryImages = [
  {
    id: 1,
    src: "https://images.unsplash.com/photo-1559128010-7c1ad6e1b6a5?w=800&q=80",
    alt: "Kepulauan Derawan",
    category: "water"
  },
  {
    id: 2,
    src: "https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=800&q=80",
    alt: "Labuan Cermin",
    category: "water"
  },
  {
    id: 3,
    src: "https://images.unsplash.com/photo-1448375240586-882707db888b?w=800&q=80",
    alt: "Hutan Hujan Borneo",
    category: "forest"
  },
  {
    id: 4,
    src: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80",
    alt: "Budaya Dayak",
    category: "culture"
  },
  {
    id: 5,
    src: "https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=800&q=80",
    alt: "Pulau Maratua",
    category: "water"
  },
  {
    id: 6,
    src: "https://images.unsplash.com/photo-1564349683136-77e08dba1ef7?w=800&q=80",
    alt: "Orangutan Borneo",
    category: "forest"
  },
  {
    id: 7,
    src: "https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?w=800&q=80",
    alt: "Air Terjun",
    category: "forest"
  },
  {
    id: 8,
    src: "https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?w=800&q=80",
    alt: "Ubur-ubur Kakaban",
    category: "water"
  }
];

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { destinations, stats, testimonials, categories, galleryImages };
}
