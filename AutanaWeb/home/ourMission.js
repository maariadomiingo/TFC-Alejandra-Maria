  let currentSlide = 0;
  const track = document.querySelector(".carousel-track");
  const slides = document.querySelectorAll(".section-container");

  function updateSlidePosition() {
    const slideWidth = slides[0].offsetWidth;
    track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
  }

  function nextSlide() {
    if (currentSlide < slides.length - 1) {
      currentSlide++;
      updateSlidePosition();
    }
  }

  function prevSlide() {
    if (currentSlide > 0) {
      currentSlide--;
      updateSlidePosition();
    }
  }

  window.addEventListener("resize", updateSlidePosition);

