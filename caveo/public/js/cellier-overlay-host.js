// NE PAS MODIFIÉ - BACKUP DU HOST POUR SUPPORTER /PUBLIC

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("addToCellierModal");
    const overlay = document.getElementById("overlay");
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
            form.action = `/public/celliers/${cellierSelect.value}/inventaires`;

            // Reset quantité
            document.getElementById("modalQuantite").value = 1;
            document.getElementById("modalQuantiteDisplay").textContent = 1;

            // Assurez-vous que la méthode est bien POST
            form.method = "POST"; // Assurez-vous que la méthode est POST
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
        form.action = `/public/celliers/${cellierSelect.value}/inventaires`;
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

    // Attach event listeners for quantity change buttons
    const minusBtn = document.querySelector(
        "#addToCellierModal button[aria-label='Diminuer la quantité']",
    );
    const plusBtn = document.querySelector(
        "#addToCellierModal button[aria-label='Augmenter la quantité']",
    );

    if (minusBtn) {
        minusBtn.addEventListener("click", () => updateModalQty(-1));
    }

    if (plusBtn) {
        plusBtn.addEventListener("click", () => updateModalQty(1));
    }
});
