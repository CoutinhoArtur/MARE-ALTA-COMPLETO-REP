// --- Script do Slider ---
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.querySelector('.slider');
    const slides = document.querySelectorAll('.slide');
    
    // Verificação para garantir que o slider existe na página
    if (!slider || slides.length === 0) {
        // Se não for a home.html, ele para aqui e não quebra o JS
        return;
    }

    const prevButton = document.getElementById('prev-slide');
    const nextButton = document.getElementById('next-slide');
    const dotsContainer = document.getElementById('slider-dots');

    let currentSlide = 0;
    const totalSlides = slides.length;

    // Criar os "dots" de navegação
    if (dotsContainer) {
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.dataset.slide = i;
            dotsContainer.appendChild(dot);
        }
    }
    
    const dots = document.querySelectorAll('.dot');

    // Função para ir para um slide específico
    function goToSlide(slideIndex) {
        if (slideIndex < 0) {
            slideIndex = totalSlides - 1;
        } else if (slideIndex >= totalSlides) {
            slideIndex = 0;
        }

        // Move o slider
        slider.style.transform = `translateX(-${slideIndex * 100}%)`;
        
        // Atualiza o "dot" ativo
        if (dots.length > 0) {
            dots.forEach(dot => dot.classList.remove('active'));
            dots[slideIndex].classList.add('active');
        }

        currentSlide = slideIndex;
    }

    // Próximo slide
    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    // Slide anterior
    function prevSlide() {
        goToSlide(currentSlide - 1);
    }

    // Event Listeners para os botões e dots
    if (nextButton) nextButton.addEventListener('click', nextSlide);
    if (prevButton) prevButton.addEventListener('click', prevSlide);

    if (dots.length > 0) {
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                goToSlide(parseInt(dot.dataset.slide));
            });
        });
    }

    // Autoplay do slider
    setInterval(nextSlide, 5000); // Muda a cada 5 segundos
});

// --- Script do Menu Mobile ---
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            
            // Troca o ícone de hamburguer para "X" e vice-versa
            const icon = menuToggle.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
                menuToggle.setAttribute('aria-label', 'Fechar menu');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
                menuToggle.setAttribute('aria-label', 'Abrir menu');
            }
        });
    }
});