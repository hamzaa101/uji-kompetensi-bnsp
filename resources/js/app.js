import Chart from 'chart.js/auto';

window.Chart = Chart;

const appState = window.KMJApp ?? {};
window.KMJApp = appState;

function isDesktop() {
    return window.matchMedia('(min-width: 1024px)').matches;
}

function initSidebar() {
    const shell = document.getElementById('app-shell');
    const sidebar = document.getElementById('app-sidebar');

    if (!shell || !sidebar || appState.sidebarInitialized) {
        return;
    }

    appState.sidebarInitialized = true;

    const storageKey = shell.dataset.sidebarStorageKey || 'kmj.sidebar.collapsed';
    const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    const closeButtons = document.querySelectorAll('[data-sidebar-close]');

    function setToggleState() {
        const expanded = isDesktop()
            ? !shell.classList.contains('sidebar-collapsed')
            : shell.classList.contains('sidebar-open');

        toggleButtons.forEach((button) => {
            button.setAttribute('aria-expanded', String(expanded));
        });
    }

    function closeMobileSidebar() {
        shell.classList.remove('sidebar-open');
        setToggleState();
    }

    function applyStoredDesktopState() {
        if (!isDesktop()) {
            shell.classList.remove('sidebar-collapsed');
            closeMobileSidebar();
            return;
        }

        shell.classList.remove('sidebar-open');

        if (localStorage.getItem(storageKey) === 'true') {
            shell.classList.add('sidebar-collapsed');
        } else {
            shell.classList.remove('sidebar-collapsed');
        }

        setToggleState();
    }

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (isDesktop()) {
                shell.classList.toggle('sidebar-collapsed');
                localStorage.setItem(storageKey, String(shell.classList.contains('sidebar-collapsed')));
            } else {
                shell.classList.toggle('sidebar-open');
            }

            setToggleState();
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeMobileSidebar);
    });

    window.addEventListener('resize', applyStoredDesktopState);
    applyStoredDesktopState();
}

async function refreshNotificationCount() {
    if (!window.notificationEndpoints?.unread || document.hidden || appState.notificationRequestInFlight) {
        return;
    }

    const badge = document.getElementById('notification-count');
    if (!badge) {
        return;
    }

    appState.notificationRequestInFlight = true;

    try {
        const response = await fetch(window.notificationEndpoints.unread, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            return;
        }

        const payload = await response.json();
        const count = Number(payload.count || 0);

        badge.textContent = count;
        badge.classList.toggle('hidden', count < 1);
    } catch {
        // Polling is progressive enhancement; navigation remains usable if it fails.
    } finally {
        appState.notificationRequestInFlight = false;
    }
}

function initNotificationPolling() {
    if (!window.notificationEndpoints?.unread) {
        return;
    }

    if (appState.notificationInterval) {
        clearInterval(appState.notificationInterval);
    }

    refreshNotificationCount();
    appState.notificationInterval = window.setInterval(refreshNotificationCount, 7000);

    if (!appState.notificationVisibilityListener) {
        appState.notificationVisibilityListener = true;
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refreshNotificationCount();
            }
        });
    }
}

function bootApp() {
    initSidebar();
    initNotificationPolling();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp, { once: true });
} else {
    bootApp();
}
