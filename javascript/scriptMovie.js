// Fonction pour vérifier si un élément est visible dans la fenêtre
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Fonction pour ajouter une classe "visible" aux éléments quand ils sont dans la vue
function handleScroll() {
    const elements = document.querySelectorAll('.reveal');
    elements.forEach((element) => {
        if (isInViewport(element)) {
            element.classList.add('visible');
        } else {
            element.classList.remove('visible');
        }
    });
}

// Animation de l'apparition des éléments au défilement
window.addEventListener('scroll', handleScroll);
document.addEventListener('DOMContentLoaded', handleScroll);

// Bande-annonce avec effet de plein écran au clic
document.addEventListener('DOMContentLoaded', function() {
    const trailer = document.querySelector('.trailer');
    if (trailer) {
        trailer.addEventListener('click', function() {
            if (trailer.requestFullscreen) {
                trailer.requestFullscreen();
            } else if (trailer.mozRequestFullScreen) { // Firefox
                trailer.mozRequestFullScreen();
            } else if (trailer.webkitRequestFullscreen) { // Chrome, Safari and Opera
                trailer.webkitRequestFullscreen();
            } else if (trailer.msRequestFullscreen) { // IE/Edge
                trailer.msRequestFullscreen();
            }
        });
    }
});

// Effet de chargement progressif des images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach((img) => {
        img.addEventListener('load', function() {
            img.classList.add('loaded');
        });
    });
});

// Bouton retour en haut
let backToTopButton = document.getElementById("backToTop");

window.onscroll = function() {
    scrollFunction();
};

function scrollFunction() {
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        backToTopButton.style.display = "block";
    } else {
        backToTopButton.style.display = "none";
    }
}

backToTopButton.addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
