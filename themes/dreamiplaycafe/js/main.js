document.addEventListener("DOMContentLoaded", () => {
  console.log("Document ready");

  // Announcement Bar Animation
  const announcementBar = document.getElementById("announcement-bar");
  if (announcementBar) {
    console.log("Announcement bar found");
    const container = announcementBar.querySelector(".announcement-container");

    if (container) {
      console.log("Container found");

      // Duplicate announcements for seamless scrolling
      const messages = Array.from(container.children);
      messages.forEach((message) => {
        const clone = message.cloneNode(true);
        container.appendChild(clone);
      });

      function getTotalWidth() {
        return container.scrollWidth / 2; // Half the cloned content width
      }

      function getSpeed() {
        const pixelsPerSecond = 50; // Set a fixed speed (adjust as needed)
        return getTotalWidth() / pixelsPerSecond;
      }

      let totalWidth = getTotalWidth();
      let speed = getSpeed();
      console.log("Total width:", totalWidth, "Speed:", speed);

      // Set container position
      gsap.set(container, { x: 0 });

      // Create infinite animation with a dynamic duration based on speed
      let animation = gsap.to(container, {
        x: -totalWidth,
        duration: speed, // Duration is dynamic based on speed
        ease: "none",
        repeat: -1,
        onUpdate: function () {
          if (Math.abs(gsap.getProperty(container, "x")) >= totalWidth) {
            gsap.set(container, { x: 0 });
          }
        },
      });

      // Pause animation on hover
      announcementBar.addEventListener("mouseenter", () => {
        animation.pause();
      });

      // Resume animation when mouse leaves
      announcementBar.addEventListener("mouseleave", () => {
        animation.play();
      });

      // Handle window resize
      window.addEventListener("resize", () => {
        totalWidth = getTotalWidth();
        speed = getSpeed();
        gsap.set(container, { x: 0 }); // Reset position
        animation.invalidate(); // Refresh the animation
        animation.duration(speed); // Update the duration based on new speed
        animation.restart();
      });
    } else {
      console.log("Container not found");
    }
  } else {
    console.log("Announcement bar not found");
  }
});

