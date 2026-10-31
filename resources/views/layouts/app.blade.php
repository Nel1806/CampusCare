<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'CampusCare' }}</title>

    <style>
        :root {
            --ink: #172033;
            --muted: #65758b;
            --line: #e3e9f0;
            --brand: #0f766e;
            --brand-dark: #0b5b55;
            --paper: #fff;
            --surface: #f5f7fb;
            --red: #b42318;
            --amber: #b54708;
            --blue: #175cd3;
            --green: #067647;
            --nav: #0b5b55;
            --nav-muted: #b8c5d4;
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: var(--surface);
            color: var(--ink);
            font: 15px/1.5 Inter, Arial, Helvetica, sans-serif;
            transition: background .2s, color .2s
        }

        body.dark {
            --ink: #edf3fb;
            --muted: #9aaabe;
            --line: #2b3d52;
            --paper: #15263a;
            --surface: #0d1928;
            --nav: #0a1624;
            --nav-muted: #9eb1c7
        }

        a {
            color: inherit;
            text-decoration: none
        }

        .shell {
            max-width: 1320px;
            margin: auto;
            padding: 0 28px
        }

        .app-layout {
            min-height: 100vh
        }

        .topbar {
            height: 72px;
            background: var(--paper);
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center
        }

        .topbar .shell {
            display: flex;
            width: 100%;
            align-items: center
        }

        .brand {
            font-weight: 800;
            letter-spacing: -.3px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            background: var(--brand);
            color: #fff;
            font-weight: 700;
            padding: 11px 16px;
            cursor: pointer;
            font-size: 14px;
            transition: .15s
        }

        .btn:hover {
            background: var(--brand-dark);
            transform: translateY(-1px)
        }

        .btn-secondary {
            background: var(--paper);
            color: var(--ink);
            border: 1px solid var(--line)
        }

        .field label {
            display: block;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 6px
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 11px 12px;
            background: var(--paper);
            font: inherit;
            color: var(--ink)
        }

        .field textarea {
            min-height: 132px;
            resize: vertical
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: 2px solid #99f6e4;
            border-color: var(--brand)
        }

        .error {
            font-size: 13px;
            color: var(--red);
            margin: 5px 0 0
        }

        .alert {
            border-radius: 9px;
            padding: 12px 14px;
            margin: 18px 0
        }

        .alert-success {
            background: #ecfdf3;
            color: #05603a;
            border: 1px solid #abefc6
        }

        .dark .alert-success {
            background: #123d32;
            color: #a7f3d0;
            border-color: #21634f
        }

        .status {
            display: inline-flex;
            align-items: center;
            border-radius: 20px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize
        }

        .status-new {
            color: var(--blue);
            background: #eff8ff
        }

        .status-assigned {
            color: #6941c6;
            background: #f9f5ff
        }

        .status-in_progress {
            color: var(--amber);
            background: #fffaeb
        }

        .status-resolved {
            color: var(--green);
            background: #ecfdf3
        }

        .card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgb(15 23 42 / 3%)
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin: 34px 0 22px
        }

        .page-head h1 {
            font-size: 30px;
            letter-spacing: -.7px;
            line-height: 1.2;
            margin: 0
        }

        .page-head p,
        .muted {
            color: var(--muted)
        }

        .login-wrap {
            max-width: 430px;
            margin: 84px auto
        }

        .login-wrap .card {
            padding: 30px
        }

        .login-wrap h1 {
            margin: 0 0 7px;
            font-size: 26px
        }

        .form-stack {
            display: grid;
            gap: 17px
        }

        .admin-nav {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px
        }

        .menu-toggle,
        .theme-toggle {
            border: 1px solid var(--line);
            background: var(--paper);
            color: var(--ink);
            border-radius: 8px;
            padding: 8px 11px;
            cursor: pointer;
            font-size: 16px
        }

        .side-menu {
            position: fixed;
            z-index: 20;
            top: 0;
            right: -340px;
            width: 310px;
            height: 100vh;
            background: var(--nav);
            color: #fff;
            padding: 26px;
            transition: right .25s ease;
            box-shadow: -18px 0 40px rgb(0 0 0 / .2)
        }

        .side-menu.open {
            right: 0
        }

        .menu-overlay {
            position: fixed;
            inset: 0;
            background: rgb(5 15 28 / .45);
            z-index: 19;
            display: none
        }

        .menu-overlay.open {
            display: block
        }

        .menu-close {
            float: right;
            background: transparent;
            border: 0;
            color: #fff;
            font-size: 24px;
            cursor: pointer
        }

        .menu-user {
            color: var(--nav-muted);
            margin: 34px 0 22px
        }

        .menu-links {
            display: grid;
            gap: 8px
        }

        .menu-links a,
        .menu-links button {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 0;
            background: transparent;
            color: #fff;
            border-radius: 8px;
            padding: 12px;
            text-align: left;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            width: 100%
        }

        .menu-links a:hover,
        .menu-links button:hover {
            background: rgb(255 255 255 / .1)
        }

        .menu-links form {
            margin: 0
        }

        .pagination {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            margin-top: 18px
        }

        .pagination a,
        .pagination span {
            padding: 7px 10px;
            border: 1px solid var(--line);
            background: var(--paper);
            border-radius: 5px;
            font-size: 13px
        }

        .side-menu {
            left: 0;
            right: auto;
            width: 72px;
            padding: 18px 14px;
            overflow: hidden;
            transition: width .25s ease
        }

        .side-menu.open {
            left: 0;
            right: auto;
            width: 310px
        }

        .menu-close {
            float: none;
            display: block;
            width: 42px;
            height: 38px;
            padding: 0;
            border-radius: 8px;
            background: rgb(255 255 255 / .1);
            font-size: 20px
        }

        .side-menu .brand {
            margin: 24px 0 0;
            white-space: nowrap
        }

        .sidebar-brand {
            color: #fff
        }

        .collapsed-brand {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            margin: 8px 0 0
        }

        .collapsed-brand .brand-logo {
            width: 42px;
            height: 42px
        }

        .side-menu.open .collapsed-brand {
            display: none
        }

        .side-menu .menu-user {
            border-bottom: 1px solid rgb(255 255 255 / .13);
            padding-bottom: 20px;
            white-space: nowrap
        }

        .side-menu .menu-user strong {
            display: block;
            color: #fff
        }

        .side-menu .menu-links {
            margin-top: 18px;
            white-space: nowrap
        }

        .side-menu:not(.open) .sidebar-brand,
        .side-menu:not(.open) .menu-user {
            opacity: 0;
            pointer-events: none
        }

        .nav-icon {
            display: inline-grid;
            flex: 0 0 20px;
            place-items: center;
            width: 20px;
            font-size: 18px
        }

        .side-menu:not(.open) .menu-links a,
        .side-menu:not(.open) .menu-links button {
            justify-content: center;
            padding: 9px
        }

        .side-menu:not(.open) .menu-links {
            gap: 4px;
            margin-top: 4px
        }

        .side-menu:not(.open) .nav-label {
            display: none
        }

        .menu-overlay {
            display: none !important
        }

        .admin-area .topbar,
        .admin-area main.shell {
            margin-left: 72px;
            width: calc(100% - 72px);
            transition: margin-left .25s ease, width .25s ease, max-width .25s ease
        }

        .admin-area main.shell {
            max-width: none
        }

        .admin-area.nav-expanded .topbar {
            margin-left: 310px;
            width: calc(100% - 310px)
        }

        .admin-area.nav-expanded main.shell {
            margin-left: 310px;
            width: calc(100% - 310px)
        }

        .alert-success {
            transition: opacity .35s ease, transform .35s ease
        }

        .alert-success.is-hiding {
            opacity: 0;
            transform: translateY(-6px)
        }

        @media (max-width: 650px) {
            .shell {
                padding: 0 16px
            }

            .page-head {
                align-items: flex-start;
                flex-direction: column;
                margin: 25px 0 18px
            }

            .page-head h1 {
                font-size: 24px
            }

            .side-menu.open {
                width: 290px
            }

            .admin-area.nav-expanded .topbar {
                margin-left: 72px;
                width: calc(100% - 72px)
            }

            .admin-area.nav-expanded main.shell {
                margin-left: 72px;
                width: calc(100% - 72px)
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    @yield('body')

    <script id="campuscare-ajax-navigation">
        (() => {
            if (window.campusCareAjaxNavigationInstalled) return;
            window.campusCareAjaxNavigationInstalled = true;

            let pageController = new AbortController();
            let navigationController = null;

            const isNavigableLink = (link, event) => {
                if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
                if (link.hasAttribute('download') || (link.target && link.target !== '_self')) return false;

                const destination = new URL(link.href, window.location.href);
                if (destination.origin !== window.location.origin) return false;
                if (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash) return false;

                return true;
            };

            const runPageScripts = (parsedPage) => {
                const originalAddEventListener = EventTarget.prototype.addEventListener;
                const controller = new AbortController();
                pageController = controller;

                EventTarget.prototype.addEventListener = function (type, listener, options) {
                    if (options && typeof options === 'object' && options.signal) {
                        return originalAddEventListener.call(this, type, listener, options);
                    }

                    const trackedOptions = typeof options === 'boolean'
                        ? { capture: options, signal: controller.signal }
                        : { ...(options || {}), signal: controller.signal };

                    return originalAddEventListener.call(this, type, listener, trackedOptions);
                };

                try {
                    parsedPage.querySelectorAll('body script').forEach((sourceScript) => {
                        if (sourceScript.id === 'campuscare-ajax-navigation') return;

                        if (sourceScript.src) {
                            const replacement = document.createElement('script');
                            replacement.src = sourceScript.src;
                            replacement.type = sourceScript.type;
                            replacement.async = false;
                            document.body.appendChild(replacement);
                            return;
                        }

                        if (sourceScript.type && sourceScript.type !== 'text/javascript' && sourceScript.type !== 'application/javascript') return;
                        new Function(sourceScript.textContent)();
                    });
                } finally {
                    EventTarget.prototype.addEventListener = originalAddEventListener;
                }
            };

            const renderResponse = async (response, historyMode, restoreScroll = null) => {
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('text/html')) throw new Error('The response is not an HTML page.');

                const html = await response.text();
                const parsedPage = new DOMParser().parseFromString(html, 'text/html');

                pageController.abort();
                document.head.innerHTML = parsedPage.head.innerHTML;
                document.title = parsedPage.title;
                document.body.innerHTML = parsedPage.body.innerHTML;
                document.body.className = parsedPage.body.className;

                const destination = new URL(response.url || window.location.href, window.location.href);
                if (historyMode === 'push') {
                    history.replaceState({ ...(history.state || {}), scrollY: window.scrollY }, '', window.location.href);
                    history.pushState({ scrollY: 0 }, '', destination.href);
                } else if (historyMode === 'replace') {
                    history.replaceState({ scrollY: 0 }, '', destination.href);
                }

                runPageScripts(parsedPage);
                window.scrollTo(0, restoreScroll ?? 0);
            };

            const navigate = async (url, { historyMode = 'push', restoreScroll = null } = {}) => {
                if (navigationController) navigationController.abort();
                navigationController = new AbortController();

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    signal: navigationController.signal,
                });

                await renderResponse(response, historyMode, restoreScroll);
            };

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[href]');
                if (!link || !isNavigableLink(link, event)) return;

                const destination = new URL(link.href, window.location.href);
                if (destination.pathname === window.location.pathname && destination.search === window.location.search && destination.hash === window.location.hash) return;

                event.preventDefault();
                navigate(destination.href).catch((error) => {
                    if (error.name !== 'AbortError') window.location.assign(destination.href);
                });
            });

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement) || event.defaultPrevented || form.target && form.target !== '_self' || form.hasAttribute('data-no-ajax')) return;

                const method = (form.method || 'GET').toUpperCase();
                const action = new URL(form.action || window.location.href, window.location.href);
                if (action.origin !== window.location.origin || !['GET', 'POST'].includes(method)) return;

                event.preventDefault();
                if (navigationController) navigationController.abort();
                let destination = action;
                let requestOptions = {
                    method,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    signal: (navigationController = new AbortController()).signal,
                };

                if (method === 'GET') {
                    const query = new URLSearchParams(new FormData(form));
                    query.forEach((value, key) => destination.searchParams.set(key, value));
                } else {
                    requestOptions.body = new FormData(form);
                }

                fetch(destination, requestOptions)
                    .then((response) => {
                        if (!response.ok && (form.hasAttribute('data-request-update-form') || form.hasAttribute('data-request-tracking-form'))) {
                            throw new Error(`Form request failed (${response.status}).`);
                        }
                        return renderResponse(response, 'push');
                    })
                    .catch((error) => {
                        if (error.name !== 'AbortError') {
                            document.dispatchEvent(new CustomEvent('campuscare:ajax-form-error', { detail: { form, error } }));
                            if (form.id !== 'maintenance-request-form' && !form.hasAttribute('data-request-tracking-form') && !form.hasAttribute('data-request-delete-form') && !form.hasAttribute('data-request-update-form')) form.submit();
                        }
                    });
            });

            window.addEventListener('popstate', (event) => {
                navigate(window.location.href, { historyMode: 'none', restoreScroll: event.state?.scrollY ?? 0 })
                    .catch(() => window.location.reload());
            });

            window.campusCareNavigate = (url) => navigate(url, { historyMode: 'replace' });
        })();

    </script>

    <script>
        document.querySelectorAll('.alert-success').forEach(alert => setTimeout(() => {
            alert.classList.add('is-hiding');

            setTimeout(() => alert.remove(), 350);
        }, 3000));
    </script>

</body>

</html>
