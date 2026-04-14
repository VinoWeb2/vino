// Gestion de l'ouverture et fermeture du panneau de filtres des utilisateurs
document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.getElementById("openFilters");
    const closeBtn = document.getElementById("closeFilters");
    const panel = document.getElementById("filterPanel");
    const overlay = document.getElementById("overlay");

    if (!openBtn || !closeBtn || !panel || !overlay) return;

    openBtn.addEventListener("click", () => {
        panel.classList.remove("translate-y-full");
        overlay.classList.remove("hidden");
        document.body.style.overflow = "hidden";
    });

    // Ferme le panneau de filtres et réactive le scroll
    function closeFilters() {
        panel.classList.add("translate-y-full");
        overlay.classList.add("hidden");
        document.body.style.overflow = "";
    }

    closeBtn.addEventListener("click", closeFilters);
    overlay.addEventListener("click", closeFilters);
});
