document.addEventListener('DOMContentLoaded', () => {
    const boutons = document.querySelectorAll('.bouton-supprimer');

    boutons.forEach(bouton => {
        bouton.addEventListener('click', function (e) {
            const message = bouton.dataset.confirm || 'Confirmer la suppression ?';

            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});