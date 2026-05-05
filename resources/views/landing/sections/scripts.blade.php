    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // AOS Initialization
            AOS.init({
                duration: 1000,
                once: true,
                offset: 100,
            });

            // Swiper Hero
            var swiperHero = new Swiper(".heroSwiper", {
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },
                speed: 3000,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                loop: true,
                watchSlidesProgress: true,
            });

            // Swiper About
            var swiperAbout = new Swiper(".aboutSwiper", {
                effect: "cards",
                grabCursor: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".about-pagination",
                    clickable: true,
                },
            });

            // Swiper Testimonials
            var swiperTestimonials = new Swiper(".testimonialSwiper", {
                slidesPerView: 1,
                spaceBetween: 40,
                grabCursor: true,
                loop: true,
                centeredSlides: false,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".testimonial-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".testimonial-next",
                    prevEl: ".testimonial-prev",
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1280: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                    },
                },
            });

            // Swiper Packages
            var swiperPackages = new Swiper(".packagesSwiper", {
                slidesPerView: 1,
                spaceBetween: 30,
                grabCursor: true,
                centeredSlides: false,
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 40,
                        allowTouchMove: true, // Allow swiping even on desktop if user wants
                    },
                },
            });
        });
    </script>
