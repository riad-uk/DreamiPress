module.exports = {
  content: [
    "./*.php",
    "./page-templates/*.php",
    "./template-parts/*.php",
    "./js/*.js",
    "./inc/*.php",
    "./templates/*.php",
    "./woocommerce/**/*.php",
  ],
  safelist: [
    "bg-gradient-to-r",
    "from-purple-500",
    "to-pink-500",
    "p-8",
    "container",
    "mx-auto",
    "shadow-xl",
    "hover:scale-105",
    "transition-transform",
    "duration-300",
  ],
  theme: {
    extend: {
      colors: {
        primary: "var(--wp--preset--color--primary)",
        secondary: "var(--wp--preset--color--secondary)",
      },
      container: {
        center: true,
        padding: "1rem",
      },
    },
  },
  plugins: [],
};
