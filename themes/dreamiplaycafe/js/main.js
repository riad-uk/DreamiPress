document.addEventListener('DOMContentLoaded', () => {
  console.log("Document ready");

  // Announcement Bar Animation
  const announcementBar = document.getElementById("announcement-bar");
  if (announcementBar) {
    console.log("Announcement bar found");
    const container = announcementBar.querySelector(".announcement-container");
    if (container) {
      console.log("Container found");
      
      // Calculate the width of one set of announcements
      const firstSet = container.querySelectorAll(".announcement-message");
      let totalWidth = 0;
      firstSet.forEach((message) => {
        totalWidth += message.offsetWidth;
      });
      console.log("Total width:", totalWidth);

      // Create the infinite scroll animation
      const tween = gsap.to(container, {
        x: -totalWidth,
        duration: 60,
        ease: "none",
        repeat: -1,
        onRepeat: () => {
          gsap.set(container, { x: 0 });
        },
        onStart: () => {
          // Ensure the container is centered before starting the animation
          const wrapper = container.parentElement;
          const wrapperWidth = wrapper.offsetWidth;
          const containerWidth = container.offsetWidth;
          const offset = (wrapperWidth - containerWidth) / 2;
          gsap.set(container, { x: offset });
        }
      });

      // Pause animation on hover
      announcementBar.addEventListener('mouseenter', () => {
        tween.pause();
      });

      // Resume animation when mouse leaves
      announcementBar.addEventListener('mouseleave', () => {
        tween.play();
      });
    } else {
      console.log("Container not found");
    }
  } else {
    console.log("Announcement bar not found");
  }
});
