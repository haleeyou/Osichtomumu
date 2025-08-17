document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("backToTop");

  // Show/hide button on scroll
  window.addEventListener("scroll", () => {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  });

  // Scroll back to top on click
  btn.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  });
});
