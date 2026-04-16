let formulaireASoumettre = null;

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("confirmModal");
    const boutonAnnuler = document.getElementById("cancelModal");
    const boutonConfirmer = document.getElementById("confirmModalBtn");
    const message = document.getElementById("confirmMessage");

    if (!modal) return;

    // Intercepte tous les boutons avec data-confirm
    document.querySelectorAll("button[data-confirm]").forEach((bouton) => {
        bouton.addEventListener("click", (e) => {
            e.preventDefault();

            formulaireASoumettre = bouton.closest("form");

            message.textContent =
                bouton.getAttribute("data-confirm") || "Êtes-vous sûr ?";

            modal.classList.remove("hidden");
        });
    });

    // Annuler
    boutonAnnuler?.addEventListener("click", () => {
        modal.classList.add("hidden");
        formulaireASoumettre = null;
    });

    // Confirmer
    boutonConfirmer?.addEventListener("click", () => {
        if (formulaireASoumettre) {
            formulaireASoumettre.submit();
        }
    });

    // clic à l’extérieur pour fermer
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("hidden");
            formulaireASoumettre = null;
        }
    });
});
