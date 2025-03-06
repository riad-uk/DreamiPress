module.exports = {
  content: [
    "./*.php",
    "./page-templates/**/*.php",
    "./template-parts/**/*.php",
    "./js/**/*.js",
    "./inc/**/*.php",
    "./templates/**/*.php",
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
    "grid",
    "grid-cols-1",
    "md:grid-cols-3",
    "gap-4",
    "rounded-lg",
    "hover:bg-blue-200",
    "hover:bg-green-200",
    "hover:bg-yellow-200",
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
