import Chart from 'chart.js/auto';

window.Chart = Chart;

async function refreshNotificationCount() {
    if (!window.notificationEndpoints?.unread) {
        return;
    }

    try {
        const response = await fetch(window.notificationEndpoints.unread);
        const payload = await response.json();
        const badge = document.getElementById('notification-count');
        if (!badge) {
            return;
        }

        badge.textContent = payload.count;
        badge.classList.toggle('hidden', payload.count < 1);
    } catch {
        // Polling is progressive enhancement; navigation remains usable if it fails.
    }
}

refreshNotificationCount();
setInterval(refreshNotificationCount, 7000);
