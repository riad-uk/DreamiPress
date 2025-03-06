/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './page-templates/*.php',
    './template-parts/*.php',
    './js/*.js',
    './inc/*.php',
    './templates/*.php',
    './woocommerce/*.php',
    './woocommerce/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        'primary': 'var(--wp--preset--color--primary)',
        'secondary': 'var(--wp--preset--color--secondary)',
      },
      container: {
        center: true,
        padding: '1rem',
      },
    },
  },
  plugins: [],
} 