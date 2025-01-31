document.addEventListener("DOMContentLoaded", function () {
  const carousel = new bootstrap.Carousel(
    document.getElementById("carouselPremium"),
    {
      interval: 3000,
      wrap: true,
      touch: true,
      pause: "hover",
    }
  );

  const currentSlideElement = document.getElementById("currentSlide");
  document
    .getElementById("carouselPremium")
    .addEventListener("slide.bs.carousel", function (event) {
      currentSlideElement.textContent = event.to + 1;
    });
});
