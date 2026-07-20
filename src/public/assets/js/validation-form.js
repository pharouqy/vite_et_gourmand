/**
 * validation-form.js
 * Validation côté client du formulaire d'inscription.
 * ⚠️ Cette validation est un confort UX — le serveur revalide TOUT.
 */

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-inscription");
  if (!form) return; // Ce fichier est chargé sur d'autres pages aussi

  // ── Regex du mot de passe (exigence du sujet) ──────────────────────
  // 10 caractères min, 1 majuscule, 1 minuscule, 1 chiffre, 1 spécial
  const REGEX_MDP = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/;

  // ── Utilitaire : afficher/masquer une erreur ────────────────────────
  function erreur(id, message) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = message;
    el.style.display = message ? "block" : "none";
  }

  function ok(id) {
    erreur(id, "");
  }

  // ── Indicateur de force du mot de passe ────────────────────────────
  const champMdp = document.getElementById("password");
  const indicateur = document.getElementById("force-mdp");

  if (champMdp && indicateur) {
    champMdp.addEventListener("input", () => {
      const val = champMdp.value;
      let score = 0;
      if (val.length >= 10) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[a-z]/.test(val)) score++;
      if (/\d/.test(val)) score++;
      if (/[\W_]/.test(val)) score++;

      const niveaux = [
        { label: "", couleur: "" },
        { label: "⬜ Très faible", couleur: "#e74c3c" },
        { label: "🟧 Faible", couleur: "#e67e22" },
        { label: "🟨 Moyen", couleur: "#f1c40f" },
        { label: "🟩 Fort", couleur: "#2ecc71" },
        { label: "✅ Très fort", couleur: "#27ae60" },
      ];

      const n = niveaux[score] ?? niveaux[0];
      indicateur.textContent = n.label;
      indicateur.style.color = n.couleur;
    });
  }

  // ── Validation à la soumission ──────────────────────────────────────
  form.addEventListener("submit", (e) => {
    let valide = true;

    // Nom
    const nom = document.getElementById("nom")?.value.trim();
    if (!nom || nom.length < 2) {
      erreur("err-nom", "Le nom doit contenir au moins 2 caractères.");
      valide = false;
    } else {
      ok("err-nom");
    }

    // Prénom
    const prenom = document.getElementById("prenom")?.value.trim();
    if (!prenom || prenom.length < 2) {
      erreur("err-prenom", "Le prénom doit contenir au moins 2 caractères.");
      valide = false;
    } else {
      ok("err-prenom");
    }

    // Email
    const email = document.getElementById("email")?.value.trim();
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !regexEmail.test(email)) {
      erreur("err-email", "Veuillez saisir une adresse email valide.");
      valide = false;
    } else {
      ok("err-email");
    }

    // Téléphone
    const tel = document.getElementById("telephone")?.value.trim();
    if (!tel || tel.length < 8) {
      erreur("err-telephone", "Numéro de téléphone invalide.");
      valide = false;
    } else {
      ok("err-telephone");
    }

    // Adresse
    const adresse = document.getElementById("adresse_postale")?.value.trim();
    if (!adresse || adresse.length < 5) {
      erreur("err-adresse", "Veuillez saisir votre adresse postale.");
      valide = false;
    } else {
      ok("err-adresse");
    }

    // Mot de passe
    const mdp = document.getElementById("password")?.value;
    if (!mdp || !REGEX_MDP.test(mdp)) {
      erreur(
        "err-password",
        "Le mot de passe doit contenir au moins 10 caractères, " +
          "1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.",
      );
      valide = false;
    } else {
      ok("err-password");
    }

    // Confirmation
    const confirm = document.getElementById("password_confirm")?.value;
    if (mdp !== confirm) {
      erreur("err-confirm", "Les deux mots de passe ne correspondent pas.");
      valide = false;
    } else {
      ok("err-confirm");
    }

    // RGPD
    const rgpd = document.getElementById("rgpd")?.checked;
    if (!rgpd) {
      erreur(
        "err-rgpd",
        "Vous devez accepter les conditions pour vous inscrire.",
      );
      valide = false;
    } else {
      ok("err-rgpd");
    }

    // Bloquer si invalide
    if (!valide) {
      e.preventDefault();
      // Scroll vers la première erreur
      document.querySelector(".form-error:not(:empty)")?.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });
    }
  });
});
