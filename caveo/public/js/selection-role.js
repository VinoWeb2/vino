/**
 * Gestion de la sélection unique des rôles via checkboxes.
 * Permet de sélectionner un seul rôle à la fois (comportement radio-like).
 */
document.addEventListener('DOMContentLoaded', function() {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Décocher tous les autres checkboxes
                roleCheckboxes.forEach(cb => {
                    if (cb !== this) {
                        cb.checked = false;
                    }
                });
            }
        });
    });
});
