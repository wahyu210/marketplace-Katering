const rupiah = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');
const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* Confirm before destructive submits: <form data-confirm="..."> */
document.addEventListener('submit', (e) => {
    const msg = e.target.dataset.confirm;
    if (msg && !window.confirm(msg)) e.preventDefault();
});

/* Mobile sidebar */
document.querySelectorAll('[data-side-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => document.querySelector('.side')?.classList.toggle('is-open'));
});
document.addEventListener('click', (e) => {
    const side = document.querySelector('.side.is-open');
    if (side && !side.contains(e.target) && !e.target.closest('[data-side-toggle]')) side.classList.remove('is-open');
});

/* Image preview for file inputs: <input type=file data-preview="#id"> */
document.querySelectorAll('input[type=file][data-preview]').forEach((input) => {
    input.addEventListener('change', () => {
        const box = document.querySelector(input.dataset.preview);
        const file = input.files?.[0];
        if (!box || !file) return;
        const img = document.createElement('img');
        img.alt = 'Pratinjau';
        img.src = URL.createObjectURL(file);
        box.replaceChildren(img);
    });
});

/* Order form: steppers + live summary */
const orderForm = document.querySelector('[data-order-form]');
if (orderForm) {
    const taxRate = parseFloat(orderForm.dataset.taxRate || '0');
    const linesBox = orderForm.querySelector('[data-summary-lines]');
    const submit = orderForm.querySelector('[data-submit]');
    const rows = [...orderForm.querySelectorAll('[data-menu]')];

    const render = () => {
        let subtotal = 0;
        let portions = 0;
        const lines = [];

        rows.forEach((row) => {
            const input = row.querySelector('input');
            const qty = Math.max(0, parseInt(input.value, 10) || 0);
            const price = parseInt(row.dataset.price, 10);
            const min = parseInt(row.dataset.min, 10);
            row.classList.toggle('is-picked', qty > 0);
            const warn = row.querySelector('[data-min-warn]');
            if (warn) warn.hidden = !(qty > 0 && qty < min);
            if (qty > 0) {
                subtotal += qty * price;
                portions += qty;
                lines.push(`<div class="summary-line"><span>${esc(row.dataset.name)} × ${qty}</span><span class="mono">${rupiah(qty * price)}</span></div>`);
            }
        });

        const tax = Math.round(subtotal * taxRate);
        linesBox.innerHTML = lines.length
            ? lines.join('')
            : '<p class="muted small" style="margin:0">Belum ada menu dipilih. Atur jumlah porsi di daftar menu.</p>';
        orderForm.querySelector('[data-portions]').textContent = portions;
        orderForm.querySelector('[data-subtotal]').textContent = rupiah(subtotal);
        orderForm.querySelector('[data-tax]').textContent = rupiah(tax);
        orderForm.querySelector('[data-total]').textContent = rupiah(subtotal + tax);
        submit.disabled = portions === 0;
    };

    rows.forEach((row) => {
        const input = row.querySelector('input');
        const min = parseInt(row.dataset.min, 10);
        row.querySelectorAll('[data-step]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const cur = parseInt(input.value, 10) || 0;
                const step = parseInt(btn.dataset.step, 10);
                let next = cur + step;
                // Jump straight to the minimum order on first increment, and back to 0 below it.
                if (step > 0 && cur < min) next = min;
                if (step < 0 && next < min) next = 0;
                input.value = Math.max(0, next);
                render();
            });
        });
        input.addEventListener('input', render);
    });

    render();
}
