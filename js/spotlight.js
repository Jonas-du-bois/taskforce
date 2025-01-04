document.addEventListener("DOMContentLoaded", () => {
  const spotlight = document.getElementById("spotlight");
  const overlay = document.getElementById("overlay");
  const body = document.body;

  const toggleSpotlight = (open) => {
    if (open) {
      spotlight.style.display = "block";
      overlay.style.display = "block";
      body.classList.add("spotlight-open");
      spotlight.querySelector("input").focus();
    } else {
      spotlight.style.display = "none";
      overlay.style.display = "none";
      body.classList.remove("spotlight-open");
    }
  };

  // Ouvrir la spotlight avec le raccourci clavier
  document.addEventListener("keydown", (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === " ") {
      e.preventDefault();
      toggleSpotlight(true);
    } else if (e.key === "Escape") {
      toggleSpotlight(false);
    }
  });

  // Activer l'écouteur sur le bouton seulement si la page est dashboard.php ou search_results.php
  if (
    window.location.pathname.endsWith("dashboard.php") ||
    window.location.pathname.endsWith("search_results.php")
  ) {
    document
      .getElementById("searchButton")
      .addEventListener("click", () => toggleSpotlight(true));
  }

  // Fermer la spotlight en cliquant sur l'overlay
  overlay.addEventListener("click", () => toggleSpotlight(false));
});
