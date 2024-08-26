const nav = document.querySelector(".nav"),
    searchIcon = document.querySelector("#searchIcon"),
    closeIcon = document.querySelector(".search-close-icon"),
    navOpenBtn = document.querySelector(".navOpenBtn"),
    navCloseBtn = document.querySelector(".navCloseBtn");

searchIcon.addEventListener("click", () => {
  nav.classList.toggle("openSearch");
  nav.classList.remove("openNav");

  if (nav.classList.contains("openSearch")) {
    searchIcon.style.display = 'none'; // Masquer l'icône de recherche
    closeIcon.style.display = 'block'; // Afficher l'icône de fermeture
  } else {
    searchIcon.style.display = 'block'; // Réafficher l'icône de recherche
    closeIcon.style.display = 'none'; // Masquer l'icône de fermeture
  }
});

closeIcon.addEventListener("click", () => {
  nav.classList.remove("openSearch");
  searchIcon.style.display = 'block'; // Réafficher l'icône de recherche
  closeIcon.style.display = 'none'; // Masquer l'icône de fermeture
});

navOpenBtn.addEventListener("click", () => {
  nav.classList.add("openNav");
  nav.classList.remove("openSearch");
  searchIcon.style.display = 'block'; // Réafficher l'icône de recherche
  closeIcon.style.display = 'none'; // Masquer l'icône de fermeture
});

navCloseBtn.addEventListener("click", () => {
  nav.classList.remove("openNav");
});

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