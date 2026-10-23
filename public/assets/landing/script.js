document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    // const menuToggle = document.querySelector('.menu-toggle');
    // const navLinks = document.querySelector('.nav-links');
    
    // if (menuToggle) {
    //     menuToggle.addEventListener('click', () => {
    //         console.log('click');
    //         navLinks.classList.toggle('active');
    //     });
    // }

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Form submission
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de envío del formulario
            alert('Mensaje enviado correctamente');
            contactForm.reset();
        });
    }

    // Intersection Observer para animaciones
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.service-card, .stat-item').forEach((el) => {
        observer.observe(el);
    });
});