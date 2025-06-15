// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Fade-in animations on scroll
    const fadeElements = document.querySelectorAll('.fade-in-element');
    
    function checkFade() {
        fadeElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementBottom = element.getBoundingClientRect().bottom;
            
            if (elementTop < window.innerHeight && elementBottom > 0) {
                element.classList.add('opacity-100');
                element.classList.remove('opacity-0');
            }
        });
    }
    
    // Initial check and add scroll event listener
    checkFade();
    window.addEventListener('scroll', checkFade);
    
    // Music player functionality (if included)
    const musicButton = document.getElementById('music-toggle');
    const audioElement = document.getElementById('background-music');
    
    if (musicButton && audioElement) {
        musicButton.addEventListener('click', function() {
            if (audioElement.paused) {
                audioElement.play();
                musicButton.innerHTML = '<i class="fas fa-pause"></i>';
                musicButton.classList.add('playing');
            } else {
                audioElement.pause();
                musicButton.innerHTML = '<i class="fas fa-play"></i>';
                musicButton.classList.remove('playing');
            }
        });
    }
    
    // Handle the RSVP form attendance radio buttons
    const attendingRadios = document.querySelectorAll('input[name="attending"]');
    const guestCountDiv = document.querySelector('.guest-count');
    
    if (attendingRadios.length && guestCountDiv) {
        attendingRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes') {
                    guestCountDiv.classList.remove('hidden');
                } else {
                    guestCountDiv.classList.add('hidden');
                }
            });
        });
    }
    
    // Initialize lightbox for gallery
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'fadeDuration': 300
        });
    }
});

// Countdown timer function
function updateCountdown() {
    const weddingDate = new Date('Dec 10, 2023 08:00:00').getTime();
    const now = new Date().getTime();
    const distance = weddingDate - now;
    
    // Time calculations
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Display the results
    if (document.getElementById('days')) {
        document.getElementById('days').innerHTML = days.toString().padStart(2, '0');
        document.getElementById('hours').innerHTML = hours.toString().padStart(2, '0');
        document.getElementById('minutes').innerHTML = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').innerHTML = seconds.toString().padStart(2, '0');
    }
    
    // If the countdown is over
    if (distance < 0) {
        clearInterval(countdownTimer);
        const countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            countdownElement.innerHTML = "<h2 class='text-3xl md:text-4xl font-great-vibes text-rose-500 mb-8'>Hari yang ditunggu telah tiba!</h2>";
        }
    }
}

// Run the countdown every second
updateCountdown();
const countdownTimer = setInterval(updateCountdown, 1000);
