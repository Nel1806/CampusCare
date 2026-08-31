<header class="topbar">
    <div class="shell">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <img class="brand-logo" src="{{ asset('images/campuscare-logo.png') }}" alt="CampusCare logo">
            CampusCare
        </a>
    </div>
</header>

<aside class="side-menu" id="side-menu">
    <button class="menu-close" type="button" id="menu-toggle" aria-label="Toggle navigation" title="Toggle navigation">☰</button>

    <a href="{{ route('admin.dashboard') }}" class="collapsed-brand" aria-label="CampusCare dashboard" title="CampusCare dashboard">
        <img class="brand-logo" src="{{ asset('images/campuscare-logo.png') }}" alt="CampusCare logo">
    </a>

    <a href="{{ route('admin.dashboard') }}" class="brand sidebar-brand">
        <img class="brand-logo" src="{{ asset('images/campuscare-logo.png') }}" alt="CampusCare logo">
        CampusCare
    </a>

    <div class="menu-user">
        <strong>{{ auth()->user()->name }}</strong>
        Administrator
    </div>

    <nav class="menu-links" aria-label="Administrator navigation">
        <a href="{{ route('admin.dashboard') }}" title="Dashboard">
            <span class="nav-icon" aria-hidden="true">▦</span>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="{{ route('admin.personnel.index') }}" title="Maintenance Personnel">
            <span class="nav-icon" aria-hidden="true">♟</span>
            <span class="nav-label">Maintenance Personnel</span>
        </a>
        <button type="button" id="theme-toggle" title="Toggle dark mode">
            <span class="nav-icon" id="theme-icon" aria-hidden="true">◐</span>
            <span class="nav-label" id="theme-label">Dark Mode</span>
        </button>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" title="Sign Out">
                <span class="nav-icon" aria-hidden="true">↪</span>
                <span class="nav-label">Sign Out</span>
            </button>
        </form>
    </nav>
</aside>

<script>
    const menu = document.getElementById('side-menu');
    const pageBody = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeLabel = document.getElementById('theme-label');

    pageBody.classList.add('admin-area');

    const setNavigation = (expanded) => {
        menu.classList.toggle('open', expanded);
        pageBody.classList.toggle('nav-expanded', expanded);
        localStorage.campusCareNavigation = expanded ? 'expanded' : 'collapsed';
    };

    setNavigation(localStorage.campusCareNavigation === 'expanded');

    document.getElementById('menu-toggle').addEventListener('click', () => {
        setNavigation(!pageBody.classList.contains('nav-expanded'));
    });

    if (localStorage.campusCareTheme === 'dark') {
        pageBody.classList.add('dark');
    }

    const updateThemeLabel = () => {
        const isDark = pageBody.classList.contains('dark');
        themeIcon.textContent = isDark ? '☀' : '◐';
        themeLabel.textContent = isDark ? 'Light Mode' : 'Dark Mode';
    };

    themeToggle.addEventListener('click', () => {
        pageBody.classList.toggle('dark');
        localStorage.campusCareTheme = pageBody.classList.contains('dark') ? 'dark' : 'light';
        updateThemeLabel();
    });

    updateThemeLabel();
</script>
