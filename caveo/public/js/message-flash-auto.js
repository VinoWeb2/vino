document.addEventListener('DOMContentLoaded', function () {
    const messages = document.querySelectorAll('.message-flash-auto');

    messages.forEach(function (message) {
        setTimeout(function () {
            message.classList.add('opacity-0', 'transition-opacity', 'duration-500');

            setTimeout(function () {
                message.remove();
            }, 500);
        }, 3000); // ⬅️ 3 secondes
    });
});