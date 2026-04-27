/**
 * Skróty w modalu "Dodaj ważenie".
 * Każdy handler = osobna funkcja, button odpala ją bezpośrednio.
 *
 *   <button onclick="WeighingShortcuts.recykler()">Recykler</button>
 *
 * Dodawanie kolejnego skrótu:
 *   1) nowy handler poniżej
 *   2) endpoint w Biuro\ShortcutController
 *   3) button w modalu
 */
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

    async function recykler() {
        const { value: tons, isConfirmed } = await Swal.fire({
            title: 'Recykler',
            html: 'Podaj wagę w tonach',
            input: 'number',
            inputAttributes: { step: '0.001', min: '0.001', max: '100' },
            inputPlaceholder: 'np. 12.5',
            showCancelButton: true,
            confirmButtonText: 'Utwórz dostawę',
            cancelButtonText: 'Anuluj',
            confirmButtonColor: '#27ae60',
            reverseButtons: true,
            preConfirm: (val) => {
                const t = parseFloat(val);
                if (!t || t <= 0) {
                    Swal.showValidationMessage('Podaj prawidłową wagę');
                    return false;
                }
                return t;
            },
        });

        if (!isConfirmed) return;

        try {
            const res = await fetch('/biuro/shortcuts/recykler', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ tons }),
            });
            const data = await res.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Dostawa utworzona',
                    text: `Recykler – ${(tons * 1000).toLocaleString('pl-PL')} kg`,
                    timer: 1800,
                    showConfirmButton: false,
                });
                location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Błąd', text: data.message ?? 'Spróbuj ponownie.' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Błąd sieci', text: e.message });
        }
    }

    window.WeighingShortcuts = { recykler };
})();
