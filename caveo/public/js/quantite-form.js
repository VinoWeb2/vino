document.addEventListener("DOMContentLoaded", () => {
    const minusBtn = document.getElementById("quantiteMinus");
    const plusBtn = document.getElementById("quantitePlus");
    const input = document.getElementById("quantiteInput");
    const display = document.getElementById("quantiteDisplay");

    if (!input || !display) return;

    function updateQty(delta) {
        let value = parseInt(input.value, 10) || 1;

        value += delta;

        if (value < 0) value = 0;
        if (value > 999) value = 999;

        input.value = value;
        display.textContent = value;
    }

    if (minusBtn) {
        minusBtn.addEventListener("click", () => updateQty(-1));
    }

    if (plusBtn) {
        plusBtn.addEventListener("click", () => updateQty(1));
    }
});