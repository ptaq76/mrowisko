{{-- Session guard: globalny przechwyt 401/419 + idle timeout --}}
<script>
(function () {
    const LOGIN_URL    = '{{ route('login') }}';
    const IDLE_MINUTES = {{ (int) (config('session.lifetime') ?: 240) }};
    let   idleTimer    = null;
    let   handling     = false;

    // Czyszczenie wszystkich nakładek (Bootstrap, SweetAlert) przed redirectem
    function cleanupOverlays() {
        try {
            document.querySelectorAll('.modal.show').forEach(m => {
                const inst = window.bootstrap?.Modal?.getInstance(m);
                if (inst) inst.hide();
            });
            document.querySelectorAll('.modal-backdrop, .swal2-container').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            if (window.Swal && Swal.isVisible()) Swal.close();
        } catch (e) { /* noop */ }
    }

    function showSessionExpired(reason) {
        if (handling) return;
        handling = true;
        cleanupOverlays();

        const fire = () => Swal.fire({
            icon: 'warning',
            title: 'Sesja wygasła',
            html: reason === 'idle'
                ? 'Z powodu długiej bezczynności zostałeś wylogowany.<br>Zaloguj się ponownie.'
                : 'Twoja sesja wygasła.<br>Zaloguj się ponownie aby kontynuować.',
            confirmButtonText: 'Zaloguj się',
            confirmButtonColor: '#1f3a5f',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then(() => { window.location.href = LOGIN_URL; });

        if (window.Swal) fire();
        else window.location.href = LOGIN_URL;
    }

    function isSessionExpiredResponse(res) {
        if (!res) return false;
        if (res.status === 401 || res.status === 419) return true;
        // Laravel czasem przekierowuje na login zamiast 401
        if (res.redirected && typeof res.url === 'string' && res.url.indexOf('/login') !== -1) {
            return true;
        }
        return false;
    }

    // 1) Globalny wrapper na fetch
    if (window.fetch) {
        const realFetch = window.fetch.bind(window);
        window.fetch = function (input, init) {
            return realFetch(input, init).then(res => {
                if (isSessionExpiredResponse(res)) {
                    showSessionExpired('expired');
                }
                return res;
            }).catch(err => {
                throw err;
            });
        };
    }

    // 2) jQuery ajax — globalny handler
    if (window.jQuery) {
        jQuery(document).ajaxError(function (_event, jqXHR) {
            if (jqXHR && (jqXHR.status === 401 || jqXHR.status === 419)) {
                showSessionExpired('expired');
            }
        });
    }

    // 3) Idle timer — IDLE_MINUTES bez ruchu = wylogowanie
    function resetIdleTimer() {
        if (handling) return;
        if (idleTimer) clearTimeout(idleTimer);
        idleTimer = setTimeout(() => showSessionExpired('idle'), IDLE_MINUTES * 60 * 1000);
    }

    ['mousemove', 'keydown', 'touchstart', 'click', 'scroll'].forEach(ev => {
        window.addEventListener(ev, resetIdleTimer, { passive: true });
    });
    resetIdleTimer();
})();
</script>
