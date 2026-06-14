/**
 * Stress Test - Food Delivery Platform API
 *
 * Skenario:
 *   1. Autentikasi driver (login & register)
 *   2. Pencarian driver tersedia (GET /api/drivers/available)
 *   3. Riwayat pengantaran (GET /api/drivers/{id}/history)
 */

import encoding from 'k6/encoding';
import http from 'k6/http';
import { check, fail, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';

// --- Konfigurasi via environment variable ---
const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000/api';
const HEALTH_URL = (__ENV.HEALTH_URL || BASE_URL.replace(/\/api\/?$/, '')) + '/up';
const VUS = Math.max(1, parseInt(__ENV.VUS || '1000', 1000) || 1000);
const DURATION = __ENV.DURATION || '1m';
const RAMP_UP = __ENV.RAMP_UP || '30s';
const RAMP_DOWN = __ENV.RAMP_DOWN || '30s';
const DRIVER_PASSWORD = __ENV.DRIVER_PASSWORD || 'password123';
const HTTP_TIMEOUT = __ENV.HTTP_TIMEOUT || '300s';
const SETUP_TIMEOUT = __ENV.SETUP_TIMEOUT || '1m';

const HTTP_PARAMS = { timeout: HTTP_TIMEOUT };

// --- Custom metrics ---
const loginSuccess = new Rate('login_success');
const registerSuccess = new Rate('register_success');
const searchSuccess = new Rate('search_success');
const historySuccess = new Rate('history_success');
const loginDuration = new Trend('login_duration', true);
const searchDuration = new Trend('search_duration', true);
const historyDuration = new Trend('history_duration', true);
const authErrors = new Counter('auth_errors');

const JSON_HEADERS = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
};

export const options = {
    scenarios: {
        // Skenario 1: Beban autentikasi driver (login + register)
        auth_load: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: RAMP_UP, target: VUS },
                { duration: DURATION, target: VUS },
                { duration: RAMP_DOWN, target: 0 },
            ],
            exec: 'authScenario',
            tags: { scenario: 'auth' },
        },
        // Skenario 2: Pencarian driver tersedia (butuh token)
        search_load: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: RAMP_UP, target: VUS },
                { duration: DURATION, target: VUS },
                { duration: RAMP_DOWN, target: 0 },
            ],
            exec: 'searchScenario',
            startTime: '10s',
            tags: { scenario: 'search' },
        },
        // Skenario 3: Riwayat pengantaran driver
        history_load: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: RAMP_UP, target: VUS },
                { duration: DURATION, target: VUS },
                { duration: RAMP_DOWN, target: 0 },
            ],
            exec: 'historyScenario',
            startTime: '10s',
            tags: { scenario: 'history' },
        },
        // Skenario 4: Alur lengkap (login → search → history)
        full_flow: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: RAMP_UP, target: Math.max(5, Math.floor(VUS / 2)) },
                { duration: DURATION, target: Math.max(5, Math.floor(VUS / 2)) },
                { duration: RAMP_DOWN, target: 0 },
            ],
            exec: 'fullFlowScenario',
            startTime: '20s',
            tags: { scenario: 'full_flow' },
        },
    },
    thresholds: {
        http_req_duration: ['p(95)<60000', 'p(99)<120000'],
        http_req_failed: ['rate<0.20'],
        login_success: ['rate>0.80'],
        search_success: ['rate>0.90'],
        history_success: ['rate>0.90'],
    },
    setupTimeout: SETUP_TIMEOUT,
};

export function setup() {
    const health = http.get(HEALTH_URL, { ...HTTP_PARAMS, tags: { name: 'health' } });
    if (health.status !== 200) {
        fail(`Aplikasi tidak merespons di ${HEALTH_URL} (status: ${health.status})`);
    }

    const mainEmail = __ENV.DRIVER_EMAIL || 'stresstest-driver@example.com';

    if (__ENV.DRIVER_TOKEN) {
        const token = __ENV.DRIVER_TOKEN;
        const driverId = parseInt(__ENV.DRIVER_ID || '', 10) || getDriverIdFromToken(token);
        return { mainToken: token, mainDriverId: driverId, mainEmail };
    }

    let token = tryLogin(mainEmail, DRIVER_PASSWORD);
    let driverId = null;

    if (!token) {
        const registered = registerDriver({
            name: 'Stress Test Driver',
            email: mainEmail,
            password: DRIVER_PASSWORD,
            vehicle: 'Motor',
        });
        token = registered.token;
        driverId = registered.driverId;
    }

    if (token && !driverId) {
        driverId = getDriverIdFromToken(token);
    }

    if (!token || !driverId) {
        fail(`Setup gagal mendapatkan token dari ${BASE_URL}`);
    }

    return { mainToken: token, mainDriverId: driverId, mainEmail };
}

function getDriverIdFromToken(token) {
    try {
        const parts = token.split('.');
        if (parts.length < 2) {
            return null;
        }
        const payload = JSON.parse(encoding.b64decode(parts[1], 'rawurl', 's'));
        const id = payload.sub ?? payload.id;
        return id ? parseInt(id, 10) : null;
    } catch (e) {
        return null;
    }
}

function authHeaders(token) {
    return {
        ...JSON_HEADERS,
        Authorization: `Bearer ${token}`,
    };
}

function safeJson(res) {
    if (!res.body || res.body.length === 0) {
        return null;
    }
    try {
        return res.json();
    } catch (e) {
        return null;
    }
}

function tryLogin(email, password) {
    const res = http.post(
        `${BASE_URL}/drivers/login`,
        JSON.stringify({ email, password }),
        { headers: JSON_HEADERS, ...HTTP_PARAMS, tags: { name: 'login' } }
    );

    if (res.status === 200) {
        const body = res.json();
        return body.token || null;
    }

    return null;
}

function registerDriver({ name, email, password, vehicle }) {
    const res = http.post(
        `${BASE_URL}/drivers/register`,
        JSON.stringify({ name, email, password, vehicle }),
        { headers: JSON_HEADERS, ...HTTP_PARAMS, tags: { name: 'register' } }
    );

    if (res.status === 201) {
        const body = res.json();
        return {
            token: body.token,
            driverId: body.driver?.id || null,
        };
    }

    return { token: null, driverId: null };
}

// Menyimpan state per VU (register hanya sekali)
const vuRegistered = {};

// --- Skenario 1: Autentikasi ---
export function authScenario(data) {
    const vuEmail = `stresstest-vu${__VU}@example.com`;

    // Hanya register satu kali per VU, lalu iterasi berikutnya langsung login
    if (!vuRegistered[__VU]) {
        group('Autentikasi Driver - Register', () => {
            const res = http.post(
                `${BASE_URL}/drivers/register`,
                JSON.stringify({
                    name: `Driver VU ${__VU}`,
                    email: vuEmail,
                    password: DRIVER_PASSWORD,
                    vehicle: 'Motor',
                }),
                { headers: JSON_HEADERS, ...HTTP_PARAMS, tags: { name: 'register' }, responseCallback: http.expectedStatuses(201, 422) }
            );

            const ok = check(res, {
                'register status 201': (r) => r.status === 201,
                'register has token': (r) => safeJson(r)?.token !== undefined,
            });

            // Email sudah terdaftar (409) juga OK — berarti VU ini sudah pernah register
            if (res.status === 201 || res.status === 422) {
                vuRegistered[__VU] = true;
            }

            registerSuccess.add(ok);
            if (!ok && res.status !== 422) authErrors.add(1);
        });
    }

    group('Autentikasi Driver - Login', () => {
        const start = Date.now();
        const res = http.post(
            `${BASE_URL}/drivers/login`,
            JSON.stringify({
                email: data.mainEmail,
                password: DRIVER_PASSWORD,
            }),
            { headers: JSON_HEADERS, ...HTTP_PARAMS, tags: { name: 'login' } }
        );

        const ok = check(res, {
            'login status 200': (r) => r.status === 200,
            'login has token': (r) => safeJson(r)?.token !== undefined,
        });

        loginSuccess.add(ok);
        loginDuration.add(Date.now() - start);
        if (!ok) authErrors.add(1);
    });

    group('Autentikasi Driver - Get User (profil)', () => {
        if (!data.mainToken) return;

        const res = http.get(`${BASE_URL}/user`, {
            headers: authHeaders(data.mainToken),
            ...HTTP_PARAMS,
            tags: { name: 'get_user' },
        });

        check(res, {
            'get user status 200': (r) => r.status === 200,
            'get user has id': (r) => safeJson(r)?.id !== undefined,
        });
    });

    sleep(0.5);
}

// --- Skenario 2: Pencarian driver ---
export function searchScenario(data) {
    if (!data.mainToken) {
        authErrors.add(1);
        return;
    }

    group('Pencarian Driver Tersedia', () => {
        const start = Date.now();
        const res = http.get(`${BASE_URL}/drivers/available`, {
            headers: authHeaders(data.mainToken),
            ...HTTP_PARAMS,
            tags: { name: 'search_available' },
        });

        const ok = check(res, {
            'search status 200': (r) => r.status === 200,
            'search returns array': (r) => Array.isArray(safeJson(r)),
        });

        searchSuccess.add(ok);
        searchDuration.add(Date.now() - start);
    });

    group('List Semua Driver', () => {
        const res = http.get(`${BASE_URL}/drivers`, {
            headers: authHeaders(data.mainToken),
            ...HTTP_PARAMS,
            tags: { name: 'list_drivers' },
        });

        check(res, {
            'list drivers status 200': (r) => r.status === 200,
        });
    });

    sleep(0.3);
}

// --- Skenario 3: Riwayat pengantaran ---
export function historyScenario(data) {
    if (!data.mainToken || !data.mainDriverId) {
        authErrors.add(1);
        return;
    }

    group('Riwayat Pengantaran Driver', () => {
        const start = Date.now();
        const res = http.get(
            `${BASE_URL}/drivers/${data.mainDriverId}/history`,
            {
                headers: authHeaders(data.mainToken),
                ...HTTP_PARAMS,
                tags: { name: 'delivery_history' },
            }
        );

        const ok = check(res, {
            'history status 200': (r) => r.status === 200,
            'history returns array': (r) => Array.isArray(safeJson(r)),
        });

        historySuccess.add(ok);
        historyDuration.add(Date.now() - start);
    });

    group('Riwayat - Akses driver lain (harus 403)', () => {
        const otherId = data.mainDriverId === 1 ? 2 : 1;
        const res = http.get(`${BASE_URL}/drivers/${otherId}/history`, {
            headers: authHeaders(data.mainToken),
            ...HTTP_PARAMS,
            tags: { name: 'history_forbidden' },
            responseCallback: http.expectedStatuses(403),
        });

        check(res, {
            'history forbidden status 403': (r) => r.status === 403,
        });
    });

    sleep(0.5);
}

// --- Skenario 4: Alur lengkap ---
export function fullFlowScenario(data) {
    group('Alur Lengkap: Login → Search → History', () => {
        // Login
        const loginRes = http.post(
            `${BASE_URL}/drivers/login`,
            JSON.stringify({
                email: data.mainEmail,
                password: DRIVER_PASSWORD,
            }),
            { headers: JSON_HEADERS, ...HTTP_PARAMS, tags: { name: 'login' } }
        );

        if (loginRes.status !== 200) {
            authErrors.add(1);
            return;
        }

        const token = safeJson(loginRes)?.token;
        if (!token) return;

        const headers = authHeaders(token);
        const driverId = getDriverIdFromToken(token);

        const searchRes = http.get(`${BASE_URL}/drivers/available`, {
            headers,
            ...HTTP_PARAMS,
            tags: { name: 'search_available' },
        });

        check(searchRes, { 'flow search ok': (r) => r.status === 200 });

        if (!driverId) return;

        const historyRes = http.get(`${BASE_URL}/drivers/${driverId}/history`, {
            headers,
            ...HTTP_PARAMS,
            tags: { name: 'delivery_history' },
        });

        check(historyRes, { 'flow history ok': (r) => r.status === 200 });
    });

    sleep(1);
}

export function handleSummary(summary) {
    const lines = [
        '',
        '══════════════════════════════════════════',
        '  STRESS TEST SUMMARY - Food Delivery API',
        '══════════════════════════════════════════',
        `  Base URL     : ${BASE_URL}`,
        `  Virtual Users: ${VUS}`,
        `  Duration     : ${DURATION}`,
        '',
        '  Thresholds:',
    ];

    for (const [name, metric] of Object.entries(summary.metrics)) {
        if (name.startsWith('login_') || name.startsWith('search_') || name.startsWith('history_')) {
            const m = metric.values;
            if (m.rate !== undefined) {
                lines.push(`    ${name}: ${(m.rate * 100).toFixed(1)}%`);
            } else if (m['p(95)'] !== undefined) {
                lines.push(`    ${name} p95: ${m['p(95)'].toFixed(0)}ms`);
            }
        }
    }

    if (summary.metrics.http_req_duration) {
        const d = summary.metrics.http_req_duration.values;
        lines.push(`    http_req_duration p95: ${d['p(95)'].toFixed(0)}ms`);
        lines.push(`    http_req_duration avg: ${d.avg.toFixed(0)}ms`);
    }

    if (summary.metrics.http_req_failed) {
        lines.push(
            `    http_req_failed: ${(summary.metrics.http_req_failed.values.rate * 100).toFixed(2)}%`
        );
    }

    lines.push('══════════════════════════════════════════', '');

    return {
        stdout: lines.join('\n'),
    };
}
