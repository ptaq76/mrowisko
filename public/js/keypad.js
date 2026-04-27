(function () {
    'use strict';

    let currentInput = null;
    let value = '';
    let allowDecimal = true;
    let minVal = null;
    let maxVal = null;

    const $sheet     = () => document.getElementById('keypadSheet');
    const $overlay   = () => document.getElementById('keypadOverlay');
    const $display   = () => document.getElementById('keypadDisplay');
    const $label     = () => document.getElementById('keypadLabel');
    const $dotBtn    = () => document.querySelector('#keypadSheet [data-key="."]');
    const $errorMsg  = () => document.getElementById('keypadError');

    function vibrate(ms) {
        try { if (navigator.vibrate) navigator.vibrate(ms || 8); } catch (e) {}
    }

    function getLabel(input) {
        if (input.dataset.keypadLabel) return input.dataset.keypadLabel;
        if (input.id) {
            const lbl = document.querySelector('label[for="' + input.id + '"]');
            if (lbl) return lbl.textContent.trim();
        }
        if (input.placeholder) return input.placeholder;
        if (input.name) return input.name;
        return '';
    }

    function open(input) {
        currentInput = input;
        value = String(input.value || '').replace(',', '.').trim();
        allowDecimal = input.dataset.decimal !== 'false';
        minVal = input.dataset.min !== undefined ? parseFloat(input.dataset.min) : null;
        maxVal = input.dataset.max !== undefined ? parseFloat(input.dataset.max) : null;

        $label().textContent = getLabel(input);
        if ($dotBtn()) $dotBtn().style.visibility = allowDecimal ? 'visible' : 'hidden';
        $errorMsg().textContent = '';

        update();
        $overlay().classList.add('open');
        $sheet().classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function close(save) {
        if (save && currentInput) {
            let v = value;
            if (v === '' || v === '.') v = '0';
            const num = parseFloat(v);

            if (minVal !== null && num < minVal) {
                showError('Min: ' + minVal);
                vibrate(40);
                return;
            }
            if (maxVal !== null && num > maxVal) {
                showError('Max: ' + maxVal);
                vibrate(40);
                return;
            }

            currentInput.value = v;
            currentInput.dispatchEvent(new Event('input',  { bubbles: true }));
            currentInput.dispatchEvent(new Event('change', { bubbles: true }));
            if (window.jQuery) {
                window.jQuery(currentInput).trigger('input').trigger('change');
            }
        }
        $overlay().classList.remove('open');
        $sheet().classList.remove('open');
        document.body.style.overflow = '';
        currentInput = null;
        value = '';
    }

    function showError(msg) {
        $errorMsg().textContent = msg;
        setTimeout(() => { $errorMsg().textContent = ''; }, 1500);
    }

    function update() {
        $display().textContent = value === '' ? '0' : value;
    }

    function press(key) {
        vibrate();
        if (key === 'del') {
            value = value.slice(0, -1);
        } else if (key === 'clear') {
            value = '';
        } else if (key === '.') {
            if (!allowDecimal) return;
            if (value.includes('.')) return;
            if (value === '') value = '0';
            value += '.';
        } else {
            // digit
            if (value === '0') value = key;
            else value += key;
        }
        update();
    }

    // ── Long-press DEL auto-repeat ──
    let delTimeout = null, delInterval = null;
    function startDelHold(e) {
        e.preventDefault();
        delTimeout = setTimeout(() => {
            delInterval = setInterval(() => press('del'), 80);
        }, 400);
    }
    function stopDelHold() {
        clearTimeout(delTimeout);
        clearInterval(delInterval);
        delTimeout = null;
        delInterval = null;
    }

    function bindKeys() {
        document.querySelectorAll('#keypadSheet [data-key]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                press(btn.dataset.key);
            });
            if (btn.dataset.key === 'del') {
                btn.addEventListener('mousedown',  startDelHold);
                btn.addEventListener('touchstart', startDelHold, { passive: false });
                ['mouseup', 'mouseleave', 'touchend', 'touchcancel'].forEach(ev =>
                    btn.addEventListener(ev, stopDelHold)
                );
            }
        });
        document.getElementById('keypadCancel').addEventListener('click', () => close(false));
        document.getElementById('keypadSave').addEventListener('click',   () => close(true));
        $overlay().addEventListener('click', () => close(false));

        // Esc zamyka (gdy ktoś podłączy fizyczną klawiaturę)
        document.addEventListener('keydown', e => {
            if (!$sheet().classList.contains('open')) return;
            if (e.key === 'Escape') { close(false); return; }
            if (e.key === 'Enter')  { close(true);  return; }
            if (e.key === 'Backspace') { press('del'); return; }
            if (e.key === '.' || e.key === ',') { press('.'); return; }
            if (/^[0-9]$/.test(e.key)) { press(e.key); }
        });
    }

    function bindInputs(root) {
        const scope = root || document;
        scope.querySelectorAll('.js-numkey:not([data-keypad-bound])').forEach(el => {
            el.setAttribute('data-keypad-bound', '1');
            el.setAttribute('readonly', 'readonly');
            el.setAttribute('inputmode', 'none');
            el.style.cursor = 'pointer';

            el.addEventListener('click', e => {
                e.preventDefault();
                open(el);
            });
            el.addEventListener('focus', () => {
                // Twardy fallback — gdyby readonly nie zadziałało, gasimy systemową
                el.blur();
                open(el);
            });
        });
    }

    function init() {
        if (!$sheet()) {
            console.warn('[keypad] Markup nie jest załadowany — brak partial _keypad.blade.php');
            return;
        }
        bindKeys();
        bindInputs();

        // Dynamicznie dodawane pola (np. modale) — auto-bind
        new MutationObserver(muts => {
            for (const m of muts) {
                m.addedNodes.forEach(n => {
                    if (n.nodeType !== 1) return;
                    bindInputs(n);
                });
            }
        }).observe(document.body, { childList: true, subtree: true });
    }

    document.addEventListener('DOMContentLoaded', init);

    // Publiczne API — np. dla pól doczepianych po init:  Keypad.refresh()
    window.Keypad = {
        open,
        close: () => close(false),
        refresh: () => bindInputs(),
    };
})();
