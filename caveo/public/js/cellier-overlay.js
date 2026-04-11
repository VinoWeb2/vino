document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addToCellierModal");
    const overlay = document.getElementById("overlay"); // si tu en as un
    const closeBtn = document.getElementById("closeModal");

    const bouteilleIdInput = document.getElementById("modalBouteilleId");
    const bouteilleNomText = document.getElementById("modalBouteilleNom");
    const cellierSelect = document.getElementById("modalCellierSelect");
    const form = document.getElementById("addToCellierForm");

    /**
     * OUVERTURE DE LA MODALE
     * Boutons "Ajouter au cellier" dans le catalogue
     */
    document.querySelectorAll(".openAddToCellierModal").forEach((button) => {
        button.addEventListener("click", () => {
            const bouteilleId = button.dataset.bouteilleId;
            const bouteilleNom = button.dataset.bouteilleNom;

            // Injecter les données
            bouteilleIdInput.value = bouteilleId;
            bouteilleNomText.textContent = bouteilleNom;

            // Mettre à jour l'action du formulaire
            form.action = `/celliers/${cellierSelect.value}/inventaires`;

            // 🔁 Reset quantité
            document.getElementById("modalQuantite").value = 1;
            document.getElementById("modalQuantiteDisplay").textContent = 1;

            // Afficher modale
            modal.classList.remove("hidden");
            if (overlay) overlay.classList.remove("hidden");
        });
    });

    /**
     * FERMETURE
     */
    function closeModal() {
        modal.classList.add("hidden");
        if (overlay) overlay.classList.add("hidden");
    }

    closeBtn.addEventListener("click", closeModal);

    if (overlay) {
        overlay.addEventListener("click", closeModal);
    }

    /**
     * ESC clavier
     */
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    /**
     * Mise à jour dynamique du cellier sélectionné
     */
    cellierSelect.addEventListener("change", () => {
        form.action = `/celliers/${cellierSelect.value}/inventaires`;
    });
});

/**
 * Gestion quantité + / -
 */
function updateModalQty(delta) {
    const input = document.getElementById("modalQuantite");
    const display = document.getElementById("modalQuantiteDisplay");

    let value = parseInt(input.value, 10) || 1;
    value += delta;

    if (value < 1) value = 1;
    if (value > 999) value = 999;

    input.value = value;
    display.textContent = value;
}
