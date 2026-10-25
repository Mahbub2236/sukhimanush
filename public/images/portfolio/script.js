/**
 * ==========================================================================
 * DR. MD. MOSHAROF HOSSAIN - PREMIUM WEBSITE SCRIPTS
 * Author: Lead Frontend Developer & Creative Interaction Architect
 * ==========================================================================
 */

document.addEventListener("DOMContentLoaded", () => {
    
    // 1. SYSTEM LOADING CONTAINER CLOSURE
    const loader = document.getElementById("loader");
    if (loader) {
        window.addEventListener("load", () => {
            setTimeout(() => {
                loader.style.opacity = "0";
                loader.style.visibility = "hidden";
            }, 600);
        });
    }

    // 2. CURSOR GLOW COORDINATE DETECTOR
    const cursorGlow = document.getElementById("cursor-glow");
    document.addEventListener("mousemove", (e) => {
        cursorGlow.style.left = `${e.clientX}px`;
        cursorGlow.style.top = `${e.clientY}px`;
    });

    // 3. PERSISTENT LIGHT & DARK THEME ENGINE
    const themeToggle = document.getElementById("theme-toggle");
    const body = document.body;
    const currentTheme = localStorage.getItem("theme") || "light-mode";

    body.classList.add(currentTheme);
    updateThemeIcon(currentTheme);

    themeToggle.addEventListener("click", () => {
        if (body.classList.contains("light-mode")) {
            body.classList.replace("light-mode", "dark-mode");
            localStorage.setItem("theme", "dark-mode");
            updateThemeIcon("dark-mode");
        } else {
            body.classList.replace("dark-mode", "light-mode");
            localStorage.setItem("theme", "light-mode");
            updateThemeIcon("light-mode");
        }
    });

    function updateThemeIcon(theme) {
        const iconSpan = themeToggle.querySelector(".toggle-icon");
        if (iconSpan) {
            iconSpan.textContent = theme === "dark-mode" ? "☀️" : "🌙";
        }
    }

    // 4. NAVIGATION BAR SCROLL DETECTOR
    const navBar = document.getElementById("main-nav");
    const backToTopBtn = document.getElementById("back-to-top");
    const scrollProgress = document.getElementById("scroll-progress");

    window.addEventListener("scroll", () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        
        // Scroll progress bar logic
        const scrollPercent = (scrollTop / docHeight) * 100;
        scrollProgress.style.width = `${scrollPercent}%`;

        // Navigation state transition
        if (scrollTop > 50) {
            navBar.classList.add("scrolled");
        } else {
            navBar.classList.remove("scrolled");
        }

        // Back to top indicator display
        if (scrollTop > 400) {
            backToTopBtn.classList.add("visible");
        } else {
            backToTopBtn.classList.remove("visible");
        }
    });

    backToTopBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

    // 5. MOBILE COLLAPSED NAVIGATION MENU
    const menuBtn = document.getElementById("menu-btn");
    const navLinks = document.querySelector(".nav-links");

    menuBtn.addEventListener("click", () => {
        navLinks.classList.toggle("active");
        menuBtn.classList.toggle("active");
    });

    // Close menu when clicking link
    document.querySelectorAll(".nav-links a").forEach(link => {
        link.addEventListener("click", () => {
            navLinks.classList.remove("active");
            menuBtn.classList.remove("active");
        });
    });

    // 6. PORTFOLIO SCROLL REVEAL INTERSECTION OBSERVER
    const revealElements = document.querySelectorAll(".scroll-reveal");
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("active");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealElements.forEach(element => {
        revealObserver.observe(element);
    });

    // 7. COUNTER NUMERICAL ANIMATION ENGINE
    const counterElements = document.querySelectorAll(".counter");
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.getAttribute("data-target"), 10);
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counterElements.forEach(counter => {
        counterObserver.observe(counter);
    });

    function animateCounter(element, target) {
        let current = 0;
        const duration = 2000; // Animation timing in milliseconds
        const steps = 50;
        const increment = target / steps;
        const stepTime = duration / steps;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString() + "+";
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString() + "+";
            }
        }, stepTime);
    }

    // 8. GALLERY ISOTOPE FILTER
    const filterButtons = document.querySelectorAll(".filter-btn");
    const galleryItems = document.querySelectorAll(".gallery-item");

    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            filterButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const filterValue = btn.getAttribute("data-filter");

            galleryItems.forEach(item => {
                if (filterValue === "all" || item.classList.contains(filterValue)) {
                    item.style.display = "block";
                    setTimeout(() => { item.style.opacity = "1"; item.style.transform = "scale(1)"; }, 10);
                } else {
                    item.style.opacity = "0";
                    item.style.transform = "scale(0.8)";
                    setTimeout(() => { item.style.display = "none"; }, 300);
                }
            });
        });
    });

    // 9. FAQ ACCORDION ENGINE
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach(item => {
        const trigger = item.querySelector(".faq-trigger");
        trigger.addEventListener("click", () => {
            const isActive = item.classList.contains("active");
            
            // Close all open FAQs
            faqItems.forEach(i => {
                i.classList.remove("active");
                i.querySelector(".faq-content").style.maxHeight = null;
                i.querySelector(".faq-trigger").setAttribute("aria-expanded", "false");
            });

            if (!isActive) {
                item.classList.add("active");
                const content = item.querySelector(".faq-content");
                content.style.maxHeight = `${content.scrollHeight + 50}px`;
                trigger.setAttribute("aria-expanded", "true");
            }
        });
    });

    // 10. BOOKING FORM ADVISORY SYSTEM
    const appointmentForm = document.getElementById("appointment-form");
    if (appointmentForm) {
        appointmentForm.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Thank you! Your clinical registration request has been submitted successfully. Our coordination officer will call you shortly to finalize your sequence number.");
            appointmentForm.reset();
        });
    }
});