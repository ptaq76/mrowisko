{{-- Wspólny helper polling. Ładowany w głównych layoutach. --}}
{{-- Dwa tryby:

  TRYB 1 — Fragment strony (najłatwiejszy):
    Oznacz w widoku dynamiczny obszar przez id, np. <div id="poll-area">...</div>
    W @section('scripts') wywołaj:
      window.pollPageFragment('poll-area', 5000);
    Helper sam pobiera tę samą stronę co X sekund, parsuje, podmienia tylko ten fragment
    jeśli różni się od bieżącego. Reszta strony (modale, formularze, scroll) nietknięta.

  TRYB 2 — Custom endpoint (gdy chcesz lekki JSON):
    window.startPolling({
        endpoint: '/route/api',
        interval: 5000,
        onUpdate: (data) => { ...aktualizuj DOM... },
    });
--}}
<script>
(function () {
    if (window.startPolling) return; // już załadowany

    /**
     * TRYB 1: pobiera bieżący URL, wycina element po ID i swap-uje innerHTML jeśli zmiana.
     * @param {string} containerId  id elementu, którego zawartość ma być odświeżana
     * @param {number} [interval]   ms między zapytaniami (domyślnie 5000)
     * @param {Function} [onSwap]   callback (newHtml) wywoływany po podmianie
     * @returns {Function} stop()
     */
    window.pollPageFragment = function (containerId, interval, onSwap) {
        interval = interval || 5000;

        const container = document.getElementById(containerId);
        if (!container) {
            console.warn('[pollPageFragment] brak elementu #' + containerId);
            return function () {};
        }

        return window.startPolling({
            endpoint: window.location.href,
            interval: interval,
            parse: 'text', // pobierz HTML jako tekst
            onUpdate: function (html) {
                let newDoc;
                try {
                    newDoc = new DOMParser().parseFromString(html, 'text/html');
                } catch (e) {
                    console.warn('[pollPageFragment] parse error', e);
                    return;
                }
                const fresh = newDoc.getElementById(containerId);
                if (!fresh) return;
                if (fresh.innerHTML === container.innerHTML) return; // nic nie zmieniono
                container.innerHTML = fresh.innerHTML;
                if (typeof onSwap === 'function') onSwap(fresh.innerHTML);
            },
        });
    };

    /**
     * TRYB 2 (lub baza dla TRYB 1): polling z Page Visibility API.
     * @param {Object}   opts
     * @param {string}   opts.endpoint
     * @param {number}   [opts.interval]      ms (domyślnie 5000)
     * @param {Function} opts.onUpdate        (data) => void
     * @param {Function} [opts.onError]
     * @param {string}   [opts.parse]         'json' (domyślnie) | 'text'
     * @param {boolean}  [opts.runImmediately] true = pierwsze zapytanie od razu (domyślnie true)
     * @returns {Function} stop()
     */
    window.startPolling = function (opts) {
        const endpoint = opts.endpoint;
        const interval = opts.interval || 5000;
        const onUpdate = opts.onUpdate;
        const parse    = opts.parse || 'json';
        const onError  = opts.onError || function (err) {
            console.warn('[polling]', endpoint, err.message || err);
        };
        const runImmediately = opts.runImmediately !== false;

        let timer = null;
        let inflight = false;

        async function tick() {
            if (document.hidden || inflight) return;
            inflight = true;
            try {
                const headers = { 'X-Requested-With': 'XMLHttpRequest' };
                if (parse === 'json') headers['Accept'] = 'application/json';

                const res = await fetch(endpoint, {
                    headers: headers,
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    if (res.status === 401 || res.status === 419) {
                        stop(); // sesja wygasła — _session_guard się tym zajmie
                        return;
                    }
                    throw new Error('HTTP ' + res.status);
                }
                const data = parse === 'json' ? await res.json() : await res.text();
                onUpdate(data);
            } catch (e) {
                onError(e);
            } finally {
                inflight = false;
            }
        }

        function start() {
            if (timer) return;
            timer = setInterval(tick, interval);
        }

        function stop() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stop();
            } else {
                start();
                tick();
            }
        });

        if (runImmediately) tick();
        start();

        return stop;
    };
})();
</script>
