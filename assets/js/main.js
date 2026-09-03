document.addEventListener('DOMContentLoaded', () => {

    /* =========================
       1. Mobile Menu Toggle
    ========================= */
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    if (mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileBtn.classList.toggle('active');
        });
    }

    /* =========================
       2. Sticky Navbar Effect
    ========================= */
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    /* =========================
       3. Smooth Scrolling
    ========================= */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    /* =========================
       4. Scroll Progress Bar
    ========================= */
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        const progressBar = document.getElementById("scroll-progress");
        if(progressBar) {
             progressBar.style.width = scrolled + "%";
        }
    });

    /* =========================
       5. Stats Animation (Intersection Observer)
    ========================= */
    const stats = document.querySelectorAll('.stat-item strong, .stat-box h3');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.style.opacity = 1;
                el.style.transform = "translateY(0)";
                // Could add number counting animation here later
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    stats.forEach(stat => {
        stat.style.opacity = 0;
        stat.style.transform = "translateY(20px)";
        stat.style.transition = "all 0.6s ease-out";
        observer.observe(stat);
    });

});
