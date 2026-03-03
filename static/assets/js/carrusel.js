const track = document.querySelector('.carousel-track');
const slides = Array.from(track.children);
const nextButton = document.querySelector('.carousel-btn.next');
const prevButton = document.querySelector('.carousel-btn.prev');
const dotsNav = document.querySelector('.carousel-dots');

let slideIndex = 0;

// Crear dots
slides.forEach((slide, idx) => {
  const dot = document.createElement('span');
  if(idx === 0) dot.classList.add('active');
  dotsNav.appendChild(dot);
});

const dots = Array.from(dotsNav.children);

function updateCarousel() {
  track.style.transform = `translateX(-${slideIndex * 100}%)`;
  dots.forEach(dot => dot.classList.remove('active'));
  dots[slideIndex].classList.add('active');
}

// Botones
nextButton.addEventListener('click', () => {
  slideIndex = (slideIndex + 1) % slides.length;
  updateCarousel();
});

prevButton.addEventListener('click', () => {
  slideIndex = (slideIndex - 1 + slides.length) % slides.length;
  updateCarousel();
});

// Dots
dots.forEach((dot, idx) => {
  dot.addEventListener('click', () => {
    slideIndex = idx;
    updateCarousel();
  });
});

// Auto-play cada 5s
setInterval(() => {
  slideIndex = (slideIndex + 1) % slides.length;
  updateCarousel();
}, 5000);

$(document).ready(function(){
  $(".menu-carousel").owlCarousel({
    loop:true,
    margin:25,
    nav:true,
    dots:true,
    autoplay:true,
    autoplayTimeout:3500,
    responsive:{
      0:{ items:1 },
      600:{ items:2 },
      1000:{ items:3 }
    }
  });

  $(".promo-carousel").owlCarousel({
    loop:true,
    margin:25,
    nav:true,
    dots:true,
    autoplay:true,
    autoplayTimeout:4000,
    responsive:{
      0:{ items:1 },
      768:{ items:2 }
    }
  });
});