import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

window.Swiper = Swiper;

document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const autoplay = (delay) => (reduceMotion ? false : { delay, disableOnInteraction: false, pauseOnMouseEnter: true });

    const hero = document.querySelector('.js-hero');
    if (hero) {
        new Swiper(hero, {
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 800,
            rewind: true,
            autoplay: autoplay(4500),
            pagination: { el: '.js-hero-pagination', clickable: true },
            navigation: { nextEl: '.js-hero-next', prevEl: '.js-hero-prev' },
        });
    }

    const about = document.querySelector('.js-about');
    if (about) {
        new Swiper(about, {
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 800,
            rewind: true,
            autoplay: autoplay(5000),
            pagination: { el: '.js-about-pagination', clickable: true },
        });
    }

    const testimonials = document.querySelector('.js-testimonials');
    if (testimonials) {
        new Swiper(testimonials, {
            slidesPerView: 1,
            spaceBetween: 24,
            grabCursor: true,
            rewind: true,
            autoplay: autoplay(6000),
            pagination: { el: '.js-testimonials-pagination', clickable: true },
            navigation: { nextEl: '.js-testimonials-next', prevEl: '.js-testimonials-prev' },
            breakpoints: {
                768: { slidesPerView: 2 },
                1280: { slidesPerView: 3 },
            },
        });
    }
});
