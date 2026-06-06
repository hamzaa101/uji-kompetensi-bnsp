import Chart from 'chart.js/auto';

window.Chart = Chart;

const appState = window.KMJApp ?? {};
window.KMJApp = appState;

const chartInstances = appState.chartInstances ?? {};
appState.chartInstances = chartInstances;

const previewObjectUrls = appState.previewObjectUrls ?? new Map();
appState.previewObjectUrls = previewObjectUrls;

function isDesktop() {
    return window.matchMedia('(min-width: 1024px)').matches;
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function appShell() {
    return document.getElementById('app-shell');
}

function clearManagedInterval(key) {
    if (appState[key]) {
        clearInterval(appState[key]);
        appState[key] = null;
    }
}

function parseJsonScript(id) {
    const element = document.getElementById(id);

    if (!element?.textContent) {
        return null;
    }

    try {
        return JSON.parse(element.textContent);
    } catch {
        return null;
    }
}

function mergeChartOptions(options = {}) {
    const defaultPlugins = {
        legend: {
            labels: {
                boxWidth: 12,
                boxHeight: 12,
                usePointStyle: true,
            },
        },
        tooltip: {
            intersect: false,
            mode: 'index',
        },
    };

    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        normalized: true,
        resizeDelay: 150,
        ...options,
        plugins: {
            ...defaultPlugins,
            ...(options.plugins ?? {}),
        },
    };
}

function getChartById(id) {
    const canvas = document.getElementById(id);

    if (!canvas) {
        return null;
    }

    return chartInstances[id] ?? Chart.getChart(canvas) ?? null;
}

function destroyExistingChart(id) {
    const existing = getChartById(id);

    if (existing) {
        existing.destroy();
    }

    delete chartInstances[id];
}

function createOrUpdateChart(id, config) {
    const canvas = document.getElementById(id);

    if (!canvas || !config) {
        return null;
    }

    const safeConfig = {
        ...config,
        options: mergeChartOptions(config.options),
    };
    const existing = getChartById(id);

    if (existing && existing.config?.type === safeConfig.type) {
        existing.data = safeConfig.data;
        existing.options = safeConfig.options;
        existing.update('none');
        chartInstances[id] = existing;
        return existing;
    }

    destroyExistingChart(id);
    chartInstances[id] = new Chart(canvas, safeConfig);

    return chartInstances[id];
}

function cleanupDetachedCharts() {
    Object.keys(chartInstances).forEach((id) => {
        const chart = chartInstances[id];

        if (!document.getElementById(id) || !chart?.canvas?.isConnected) {
            destroyExistingChart(id);
        }
    });
}

window.KlinikCharts = {
    instances: chartInstances,
    createOrUpdateChart,
    destroyExistingChart,
};

function initSidebar() {
    const shell = appShell();
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

function notificationEndpoints() {
    const shell = appShell();

    return {
        unread: shell?.dataset.notificationUnreadUrl || window.notificationEndpoints?.unread,
        latest: shell?.dataset.notificationLatestUrl || window.notificationEndpoints?.latest,
    };
}

function stopNotificationPolling() {
    clearManagedInterval('notificationInterval');

    if (appState.notificationAbortController) {
        appState.notificationAbortController.abort();
        appState.notificationAbortController = null;
    }
}

async function refreshNotificationCount() {
    const endpoints = notificationEndpoints();

    if (!endpoints.unread || document.hidden || appState.notificationRequestInFlight) {
        return;
    }

    const badge = document.getElementById('notification-count');
    if (!badge) {
        stopNotificationPolling();
        return;
    }

    appState.notificationRequestInFlight = true;
    appState.notificationAbortController = new AbortController();

    try {
        const response = await fetch(endpoints.unread, {
            headers: { Accept: 'application/json' },
            signal: appState.notificationAbortController.signal,
        });

        if (!response.ok) {
            return;
        }

        const payload = await response.json();
        const count = Number(payload.count || 0);

        badge.textContent = count;
        badge.classList.toggle('hidden', count < 1);
    } catch (error) {
        if (error.name !== 'AbortError') {
            badge?.classList.add('hidden');
        }
    } finally {
        appState.notificationRequestInFlight = false;
        appState.notificationAbortController = null;
    }
}

function initNotificationPolling() {
    const endpoints = notificationEndpoints();

    if (!endpoints.unread || !document.getElementById('notification-count')) {
        stopNotificationPolling();
        return;
    }

    clearManagedInterval('notificationInterval');
    refreshNotificationCount();
    appState.notificationInterval = window.setInterval(refreshNotificationCount, 15000);

    if (!appState.notificationVisibilityListener) {
        appState.notificationVisibilityListener = true;
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refreshNotificationCount();
            }
        });
    }
}

function initNotificationReadButtons() {
    if (appState.notificationReadListener) {
        return;
    }

    appState.notificationReadListener = true;

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-notification-read]');

        if (!button) {
            return;
        }

        event.preventDefault();

        if (button.dataset.requestInFlight === 'true') {
            return;
        }

        const endpoint = button.dataset.notificationRead;

        if (!endpoint) {
            return;
        }

        button.dataset.requestInFlight = 'true';
        button.disabled = true;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
            });

            if (!response.ok) {
                button.disabled = false;
                return;
            }

            button.remove();
            refreshNotificationCount();
        } catch {
            button.disabled = false;
        } finally {
            delete button.dataset.requestInFlight;
        }
    });
}

function initAdminDashboardCharts() {
    const payload = parseJsonScript('admin-dashboard-chart-data');

    if (!payload) {
        return;
    }

    const daily = payload.dailySales ?? [];
    createOrUpdateChart('admin-sales-chart', {
        type: 'line',
        data: {
            labels: daily.map((row) => row.period),
            datasets: [{
                label: 'Omzet',
                data: daily.map((row) => Number(row.revenue || 0)),
                borderColor: '#0f766e',
                backgroundColor: 'rgba(15, 118, 110, 0.12)',
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 3,
                tension: 0.25,
                fill: true,
            }],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                },
            },
        },
    });

    const statuses = payload.statusRecap ?? {};
    createOrUpdateChart('admin-status-chart', {
        type: 'bar',
        data: {
            labels: Object.keys(statuses),
            datasets: [{
                label: 'Order',
                data: Object.values(statuses).map((value) => Number(value || 0)),
                backgroundColor: '#2563eb',
                borderRadius: 4,
                maxBarThickness: 48,
            }],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                },
            },
        },
    });
}

function stopMonitoringPolling() {
    clearManagedInterval('monitoringInterval');

    if (appState.monitoringAbortController) {
        appState.monitoringAbortController.abort();
        appState.monitoringAbortController = null;
    }
}

function updateMetricText(metrics, key, value) {
    const element = metrics.querySelector(`[data-key="${key}"]`);

    if (element) {
        element.textContent = value;
    }
}

function initMonitoringPolling() {
    const metrics = document.querySelector('[data-monitoring-endpoint]');

    if (!metrics) {
        stopMonitoringPolling();
        return;
    }

    const endpoint = metrics.dataset.monitoringEndpoint;

    if (!endpoint) {
        return;
    }

    const interval = Math.max(Number(metrics.dataset.pollInterval || 10000), 10000);

    clearManagedInterval('monitoringInterval');

    const updateMetrics = async () => {
        if (!metrics.isConnected) {
            stopMonitoringPolling();
            return;
        }

        if (document.hidden || appState.monitoringRequestInFlight) {
            return;
        }

        appState.monitoringRequestInFlight = true;
        appState.monitoringAbortController = new AbortController();

        try {
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                signal: appState.monitoringAbortController.signal,
            });

            if (!response.ok) {
                return;
            }

            const metric = await response.json();
            updateMetricText(metrics, 'memory_usage', `${(metric.memory_usage / 1024 / 1024).toFixed(2)} MB`);
            updateMetricText(metrics, 'disk_usage', `${(metric.disk_usage / 1024 / 1024 / 1024).toFixed(2)} GB`);
            updateMetricText(metrics, 'queue_pending', metric.queue_pending);
            updateMetricText(metrics, 'request_count', metric.request_count);
            updateMetricText(metrics, 'error_count', metric.error_count);
            updateMetricText(metrics, 'critical_notification_count', metric.critical_notification_count);
            updateMetricText(metrics, 'avg_response_time', `${metric.avg_response_time} ms`);
        } catch {
            // Monitoring is informational; the page remains usable if polling fails.
        } finally {
            appState.monitoringRequestInFlight = false;
            appState.monitoringAbortController = null;
        }
    };

    appState.monitoringInterval = window.setInterval(updateMetrics, interval);
}

function closeAutocomplete(box) {
    if (!box) {
        return;
    }

    box.replaceChildren();
    box.classList.add('hidden');
}

function initCatalogAutocomplete() {
    document.querySelectorAll('[data-autocomplete-url]').forEach((input) => {
        if (input.dataset.autocompleteInitialized === 'true') {
            return;
        }

        const box = document.querySelector(input.dataset.autocompleteTarget);
        const endpoint = input.dataset.autocompleteUrl;

        if (!box || !endpoint) {
            return;
        }

        input.dataset.autocompleteInitialized = 'true';
        let timer = null;
        let controller = null;

        input.addEventListener('input', () => {
            const query = input.value.trim();

            window.clearTimeout(timer);

            if (controller) {
                controller.abort();
                controller = null;
            }

            if (query.length < 2) {
                closeAutocomplete(box);
                return;
            }

            timer = window.setTimeout(async () => {
                controller = new AbortController();

                try {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set('q', query);

                    const response = await fetch(url.toString(), {
                        headers: { Accept: 'application/json' },
                        signal: controller.signal,
                    });

                    if (!response.ok) {
                        closeAutocomplete(box);
                        return;
                    }

                    const rows = await response.json();
                    box.replaceChildren();

                    rows.forEach((row) => {
                        const link = document.createElement('a');
                        link.href = row.url;
                        link.textContent = row.label;
                        box.appendChild(link);
                    });

                    box.classList.toggle('hidden', rows.length === 0);
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        closeAutocomplete(box);
                    }
                } finally {
                    controller = null;
                }
            }, 300);
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeAutocomplete(box);
            }
        });
    });

    if (!appState.autocompleteOutsideListener) {
        appState.autocompleteOutsideListener = true;
        document.addEventListener('click', (event) => {
            document.querySelectorAll('[data-autocomplete-url]').forEach((input) => {
                const box = document.querySelector(input.dataset.autocompleteTarget);

                if (box && !input.contains(event.target) && !box.contains(event.target)) {
                    closeAutocomplete(box);
                }
            });
        });
    }
}

function acceptsFile(input, file) {
    const accept = (input.accept || '')
        .split(',')
        .map((value) => value.trim().toLowerCase())
        .filter(Boolean);

    if (accept.length === 0) {
        return true;
    }

    return accept.some((rule) => {
        if (rule.endsWith('/*')) {
            return file.type.toLowerCase().startsWith(rule.slice(0, -1));
        }

        if (rule.startsWith('.')) {
            return file.name.toLowerCase().endsWith(rule);
        }

        return file.type.toLowerCase() === rule;
    });
}

function clearPreview(input, preview) {
    const objectUrl = previewObjectUrls.get(input);

    if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        previewObjectUrls.delete(input);
    }

    if (preview) {
        preview.removeAttribute('src');
        preview.classList.add('hidden');
    }
}

function initFilePreviews() {
    document.querySelectorAll('[data-preview-target]').forEach((input) => {
        if (input.dataset.previewInitialized === 'true') {
            return;
        }

        const preview = document.querySelector(input.dataset.previewTarget);

        if (!preview) {
            return;
        }

        input.dataset.previewInitialized = 'true';

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            clearPreview(input, preview);
            input.setCustomValidity('');

            if (!file) {
                return;
            }

            if (!acceptsFile(input, file)) {
                input.setCustomValidity('Format file tidak didukung.');
                input.reportValidity();
                input.value = '';
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            previewObjectUrls.set(input, objectUrl);
            preview.src = objectUrl;
            preview.classList.remove('hidden');
        });
    });

    if (!appState.previewUnloadListener) {
        appState.previewUnloadListener = true;
        window.addEventListener('beforeunload', () => {
            previewObjectUrls.forEach((url) => URL.revokeObjectURL(url));
            previewObjectUrls.clear();
        });
    }
}

function bootApp() {
    cleanupDetachedCharts();
    initSidebar();
    initNotificationPolling();
    initNotificationReadButtons();
    initAdminDashboardCharts();
    initMonitoringPolling();
    initCatalogAutocomplete();
    initFilePreviews();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp, { once: true });
} else {
    bootApp();
}
