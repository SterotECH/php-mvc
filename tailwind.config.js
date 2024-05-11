/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
content: require('fast-glob').sync([
    'resources/**/*.{blade.php,blade.md,md,html,vue,php}',
    'app/**/*.php',
    '!resources/**/_tmp/*'
  ],{ dot: true }),
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
};
