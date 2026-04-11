document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("cellierOverlay");
    const modal = document.getElementById("cellierModal");
    const closeBtn = document.getElementById("closeCellierModal");
    const cancelBtn = document.getElementById("cancelCellierModal");
    const form = document.getElementById("addToCellierForm");

    const bouteilleIdInput = document.getElementById("modalBouteilleId");
    const bouteilleNomText = document.getElementById("modalBouteilleNom");
    const cellierSelect = document.getElementById("modalCellierId");

    document.querySelectorAll(".openAddToCellierModal").forEach((button) => {
        button.addEventListener("click", () => {
            const bouteilleId = button.dataset.bouteilleId;
            const bouteilleNom = button.dataset.bouteilleNom;

            bouteilleIdInput.value = bouteilleId;
            bouteilleNomText.textContent = bouteilleNom;
            form.action = `/celliers/${cellierSelect.value}/inventaires`;

            overlay.classList.remove("hidden");
            modal.classList.remove("hidden");
        });
    });

    cellierSelect.addEventListener("change", () => {
        form.action = `/celliers/${cellierSelect.value}/inventaires`;
    });

    function closeModal() {
        overlay.classList.add("hidden");
        modal.classList.add("hidden");
    }

    closeBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);
    overlay.addEventListener("click", closeModal);

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeModal();
    });
});
