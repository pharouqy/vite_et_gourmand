/**
 * filtres-menus.js
 * Filtrage dynamique des menus via Fetch API (AJAX).
 * Aucun rechargement de page.
 */

document.addEventListener("DOMContentLoaded", () => {
  // ── Éléments DOM ─────────────────────────────────────────────────
  const grille = document.getElementById("menus-grille");
  const compteur = document.getElementById("menus-count");
  const btnReset = document.getElementById("btn-reset-filtres");

  // Tous les champs de filtres
  const champs = document.querySelectorAll(
    "#filtres-form input, #filtres-form select",
  );

  // ── Debounce : attend 400ms après la dernière frappe ─────────────
  // Évite d'envoyer une requête à chaque caractère tapé
  let timer = null;

  function debounce(fn, delai = 400) {
    clearTimeout(timer);
    timer = setTimeout(fn, delai);
  }

  // ── Construit les paramètres de l'URL depuis les filtres ─────────
  function construireParams() {
    const params = new URLSearchParams();

    champs.forEach((champ) => {
      if (champ.value.trim() !== "") {
        params.append(champ.name, champ.value.trim());
      }
    });

    return params;
  }

  // ── Génère le HTML d'une carte menu ─────────────────────────────
  function carteMenu(menu) {
    const prix =
      parseFloat(menu.prix_par_personne).toLocaleString("fr-FR") + " DA";
    const regime = menu.regime
      ? `<span class="badge badge-regime">${echapper(menu.regime)}</span>`
      : "";
    const theme = menu.theme
      ? `<span class="badge badge-secondary ms-1">${echapper(menu.theme)}</span>`
      : "";
    const dispo =
      parseInt(menu.quantite_restante) > 0
        ? `<span class="badge badge-accepte">Disponible</span>`
        : `<span class="badge badge-annulee">Complet</span>`;

    return `
            <article class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h3 class="h5 mb-0" style="color:#fff;">
                        ${echapper(menu.titre)}
                    </h3>
                    ${dispo}
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3"
                       style="font-size: var(--font-size-sm);
                              display: -webkit-box;
                              -webkit-line-clamp: 2;
                              -webkit-box-orient: vertical;
                              overflow: hidden;">
                        ${echapper(menu.description ?? "")}
                    </p>

                    <div class="d-flex justify-content-between
                                align-items-center mb-3">
                        <span class="card-prix">${prix} / pers.</span>
                        <span class="text-muted"
                              style="font-size: var(--font-size-sm);">
                            min. ${menu.nombre_personne_minimum} pers.
                        </span>
                    </div>

                    <div class="mb-3">${regime}${theme}</div>

                    <a href="/menus/detail?id=${menu.menu_id}"
                       class="btn btn-primary w-100">
                        Voir le détail
                    </a>
                </div>
            </article>
        `;
  }

  // ── Message vide ─────────────────────────────────────────────────
  function afficherVide() {
    grille.innerHTML = `
            <div class="text-center py-xl" style="grid-column:1/-1;">
                <p style="font-size: var(--font-size-xl);">🍽</p>
                <p class="text-muted">
                    Aucun menu ne correspond à vos critères.<br>
                    <button type="button"
                            onclick="document.getElementById('btn-reset-filtres').click()"
                            class="btn btn-outline mt-3">
                        Voir tous les menus
                    </button>
                </p>
            </div>
        `;
  }

  // ── Échappement XSS côté JS ──────────────────────────────────────
  function echapper(str) {
    const d = document.createElement("div");
    d.textContent = str;
    return d.innerHTML;
  }

  // ── Requête principale ───────────────────────────────────────────
  async function chargerMenus() {
    const params = construireParams();

    // Indicateur de chargement
    grille.innerHTML = `
            <div class="text-center py-xl" style="grid-column:1/-1;">
                <div class="spinner-border"
                     style="color:var(--color-primary);"
                     role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        `;

    try {
      const reponse = await fetch("/api/menus?" + params.toString());

      if (!reponse.ok) {
        throw new Error("Erreur serveur : " + reponse.status);
      }

      const data = await reponse.json();

      // Mise à jour du compteur
      compteur.textContent =
        data.count === 0
          ? "Aucun menu trouvé"
          : `${data.count} menu${data.count > 1 ? "s" : ""} trouvé${data.count > 1 ? "s" : ""}`;

      // Mise à jour de la grille
      if (data.count === 0) {
        afficherVide();
        return;
      }

      grille.innerHTML = data.menus.map(carteMenu).join("");
    } catch (erreur) {
      console.error("[Filtres menus]", erreur);
      grille.innerHTML = `
                <div class="alerte alerte-danger" style="grid-column:1/-1;">
                    Une erreur est survenue. Veuillez recharger la page.
                </div>
            `;
    }
  }

  // ── Écoute les changements sur tous les filtres ──────────────────
  champs.forEach((champ) => {
    const evenement = champ.tagName === "SELECT" ? "change" : "input";
    champ.addEventListener(evenement, () => debounce(chargerMenus));
  });

  // ── Réinitialisation ─────────────────────────────────────────────
  btnReset.addEventListener("click", () => {
    champs.forEach((champ) => {
      champ.value = "";
    });
    chargerMenus();
  });

  // ── Chargement initial (tous les menus) ──────────────────────────
  chargerMenus();
});
