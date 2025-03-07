document.addEventListener("DOMContentLoaded", () => {
  console.log("Document ready");

  // Select announcement bar and container
  const announcementBar = document.getElementById("announcement-bar");
  if (!announcementBar) {
    console.log("Announcement bar not found");
    return;
  }
  console.log("Announcement bar found");

  const container = announcementBar.querySelector(".announcement-container");
  if (!container) {
    console.log("Container not found");
    return;
  }
  console.log("Container found");

  // Ensure announcements exist
  const messages = container.children;
  if (messages.length === 0) {
    console.log("No announcements found");
    return;
  }

  // Duplicate content manually for seamless scrolling
  container.innerHTML += container.innerHTML;

  function getTotalWidth() {
    return container.scrollWidth / 2; // Half the duplicated width
  }

  function getSpeed() {
    const pixelsPerSecond = 50; // Adjust scrolling speed
    return getTotalWidth() / pixelsPerSecond;
  }

  let totalWidth = getTotalWidth();
  let speed = getSpeed();

  console.log("Total width:", totalWidth, "Speed:", speed);

  // Ensure the container is wide enough for scrolling
  container.style.whiteSpace = "nowrap";
  container.style.display = "flex";

  // Apply GSAP animation
  gsap.set(container, { x: 0 });

  let animation = gsap.to(container, {
    x: -totalWidth,
    duration: speed,
    ease: "none",
    repeat: -1,
  });

  // Pause animation on hover
  announcementBar.addEventListener("mouseenter", () => animation.pause());
  announcementBar.addEventListener("mouseleave", () => animation.play());

  // Handle window resize
  window.addEventListener("resize", () => {
    totalWidth = getTotalWidth();
    speed = getSpeed();
    animation.invalidate(); // Refresh GSAP
    animation.duration(speed);
    animation.restart();
  });
});
