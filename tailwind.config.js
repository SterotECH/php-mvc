/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.{html, php, js}',
    './resources/views/**/*.php',
    './app/Core/**/*.php',
    './public/index.php'
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        colors: {
          primary: {
            "50":"#eff6ff",
            "100":"#dbeafe",
            "200":"#bfdbfe",
            "300":"#93c5fd",
            "400":"#60a5fa",
            "500":"#3b82f6",
            "600":"#2563eb",
            "700":"#1d4ed8",
            "800":"#1e40af",
            "900":"#1e3a8a",
            "950":"#172554"
          },
          secondary: '#4A90E2',
      },
      fontFamily: {
        sans: ['Helvetica', 'Arial', 'sans-serif'],
        serif: ['Georgia', 'serif'],
      },
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
}


