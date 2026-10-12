/**
 * avis.js — Composant notation 1-5 étoiles interactif
 */
document.addEventListener("DOMContentLoaded", () => {
  const etoiles = document.querySelectorAll(".etoile");
  if (!etoiles.length) return;

  const COULEUR_ACTIVE = "var(--color-accent-dark)";
  const COULEUR_NEUTRE = "var(--color-border)";

  function colorier(jusqu_a) {
    etoiles.forEach((e) => {
      e.style.color =
        parseInt(e.dataset.val) <= jusqu_a ? COULEUR_ACTIVE : COULEUR_NEUTRE;
    });
  }

  // Survol
  etoiles.forEach((e) => {
    e.addEventListener("mouseover", () => colorier(parseInt(e.dataset.val)));
    e.addEventListener("mouseout", () => {
      // Restaurer la note sélectionnée
      const cochee = document.querySelector('input[name="note"]:checked');
      colorier(cochee ? parseInt(cochee.value) : 0);
    });
    e.addEventListener("click", () => {
      const radio = e.previousElementSibling;
      radio.checked = true;
      colorier(parseInt(e.dataset.val));
    });
  });
});
