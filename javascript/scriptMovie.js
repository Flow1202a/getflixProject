// scriptMovie.js

// Fonction pour vérifier si un élément est visible dans le viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top < window.innerHeight &&
        rect.left < window.innerWidth &&
        rect.bottom > 0 &&
        rect.right > 0
    );
}

// Fonction pour charger les images en lazy loading
function loadImages() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach((img) => {
        if (isElementInViewport(img)) {
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
            img.classList.add('show'); // Ajouter la classe show directement si l'image est déjà visible
        }
    });
}

// Fonction pour ajouter la classe 'show' aux éléments visibles
function handleScroll() {
    loadImages(); // Charger les images visibles
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach((reveal) => {
        if (isElementInViewport(reveal)) {
            reveal.classList.add('show');
        } else {
            // Optionnel: Supprime la classe 'show' si l'élément sort du viewport
            reveal.classList.remove('show');
        }
    });
}

// Événement de défilement
window.addEventListener('scroll', handleScroll);

// Événement de chargement pour s'assurer que les éléments visibles au chargement sont également traités
window.addEventListener('load', () => {
    handleScroll(); // Appeler handleScroll pour vérifier les éléments visibles dès le chargement
    loadImages();   // Appeler loadImages pour charger les images visibles dès le chargement
});

// Optionnel: ajouter un bouton de retour en haut avec une animation de défilement fluide
const backToTopButton = document.getElementById('backToTop');
if (backToTopButton) {
    backToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Afficher ou masquer le bouton en fonction du défilement
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) { // Afficher le bouton si on a défilé de 300px
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
}

const nav = document.querySelector(".nav"),
    searchIcon = document.querySelector("#searchIcon"),
    navOpenBtn = document.querySelector(".navOpenBtn"),
    navCloseBtn = document.querySelector(".navCloseBtn");

searchIcon.addEventListener("click", () => {
    nav.classList.toggle("openSearch");
    nav.classList.remove("openNav");
    if (nav.classList.contains("openSearch")) {
        return searchIcon.classList.replace("uil-search", "uil-times");
    }
    searchIcon.classList.replace("uil-times", "uil-search");
});

navOpenBtn.addEventListener("click", () => {
    nav.classList.add("openNav");
    nav.classList.remove("openSearch");
    searchIcon.classList.replace("uil-times", "uil-search");
});
navCloseBtn.addEventListener("click", () => {
    nav.classList.remove("openNav");
});
