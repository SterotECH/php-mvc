/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.{html, php, js}',
    './resources/views/**/*.php',
    './public/index.php'
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#FF6347',
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


