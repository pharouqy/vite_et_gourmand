/**
 * commande.js
 * Calcul du prix en temps réel + règle remise 10% (US-3.2)
 */

const REMISE_SEUIL = 5; // +5 personnes au-delà du minimum
const REMISE_TAUX = 0.1; // 10%

function mettreAJourInfoMenu(select) {
  const option = select.options[select.selectedIndex];
  const min = parseInt(option.dataset.min || "0");
  const prix = parseFloat(option.dataset.prix || "0");
  const infoMin = document.getElementById("info-minimum");
  const nbChamp = document.getElementById("nombre_personne");
  const recapPrix = document.getElementById("recap-prix");

  if (!min) {
    infoMin.textContent = "";
    recapPrix.style.display = "none";
    return;
  }

  infoMin.textContent = `Minimum requis : ${min} personnes.`;
  nbChamp.min = min;

  // Recalculer si le nombre est déjà saisi
  if (nbChamp.value) mettreAJourPrix(prix, min);
}

function mettreAJourPrix(prix, min) {
  const nbChamp = document.getElementById("nombre_personne");
  const recapPrix = document.getElementById("recap-prix");
  const nb = parseInt(nbChamp.value || "0");

  if (!nb || nb < min) {
    recapPrix.style.display = "none";
    return;
  }

  const prixBrut = prix * nb;
  const remise = nb >= min + REMISE_SEUIL;
  const montantRemise = remise ? prixBrut * REMISE_TAUX : 0;
  const prixNet = prixBrut - montantRemise;

  let html = `
        <strong>Détail du prix :</strong><br>
        ${nb} personnes × ${formatPrix(prix)} = ${formatPrix(prixBrut)}
    `;

  if (remise) {
    html += `<br>
        🎉 Remise 10% appliquée
        (${nb} pers. ≥ ${min + REMISE_SEUIL} pers.) :
        − ${formatPrix(montantRemise)}`;
  }

  html += `<br><strong>Total menu : ${formatPrix(prixNet)}</strong>`;

  recapPrix.innerHTML = html;
  recapPrix.style.display = "block";
}

function formatPrix(montant) {
  return (
    new Intl.NumberFormat("fr-FR", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(montant) + " DA"
  );
}

// Écouter les changements sur le nombre de personnes
document.addEventListener("DOMContentLoaded", () => {
  const nbChamp = document.getElementById("nombre_personne");
  const select = document.getElementById("menu_id");

  if (!nbChamp || !select) return;

  nbChamp.addEventListener("input", () => {
    const option = select.options[select.selectedIndex];
    const min = parseInt(option.dataset.min || "0");
    const prix = parseFloat(option.dataset.prix || "0");
    if (min && prix) mettreAJourPrix(prix, min);
  });

  // Déclencher si menu déjà sélectionné (retour arrière)
  if (select.value) mettreAJourInfoMenu(select);
});
