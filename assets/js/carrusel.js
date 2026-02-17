// ===================== CARRUSEL PREMIUM =====================
(function () {

  const track = document.querySelector(".carousel-track");
  const slides = document.querySelectorAll(".carousel-slide");
  const nextBtn = document.querySelector(".carousel-btn.next");
  const prevBtn = document.querySelector(".carousel-btn.prev");
  const dotsContainer = document.querySelector(".carousel-dots");

  if (!track || slides.length === 0) return;

  let index = 0;
  let timer;

  // ===================== DOTS =====================
  if (dotsContainer) {
    slides.forEach((_, i) => {
      const dot = document.createElement("button");
      if (i === 0) dot.classList.add("active");

      dot.addEventListener("click", () => {
        goTo(i);
        resetTimer();
      });

      dotsContainer.appendChild(dot);
    });
  }

  // ===================== UPDATE =====================
  function updateSlides() {
    track.style.transform = `translateX(-${index * 100}%)`;

    if (dotsContainer) {
      dotsContainer.querySelectorAll("button")
        .forEach(dot => dot.classList.remove("active"));

      if (dotsContainer.children[index]) {
        dotsContainer.children[index].classList.add("active");
      }
    }
  }

  function goTo(i) {
    index = (i + slides.length) % slides.length;
    updateSlides();
  }

  function next() {
    goTo(index + 1);
  }

  function prev() {
    goTo(index - 1);
  }

  // ===================== CONTROLES =====================
  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      next();
      resetTimer();
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      prev();
      resetTimer();
    });
  }

  // ===================== AUTO PLAY =====================
  function startTimer() {
    timer = setInterval(next, 5000);
  }

  function resetTimer() {
    clearInterval(timer);
    startTimer();
  }

  updateSlides();
  startTimer();

})();
