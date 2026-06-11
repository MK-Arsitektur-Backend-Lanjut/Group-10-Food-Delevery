import http from 'k6/http';
import { check, sleep } from 'k6';


export const options = {
    scenarios: {
        load_test: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '30s', target: 5000 },   // Naik ke 5.000 user dalam 30 detik
                { duration: '30s', target: 10000 },  // Naik ke 10.000 user dalam 30 detik
                { duration: '5m', target: 10000 },   // Bertahan di 10.000 user selama 2 menit
                { duration: '30s', target: 0 },      // Turun ke 0 (cooldown)
            ],
            gracefulRampDown: '30s',
        },
    },
    httpTimeout: '300s',
};

export default function () {
    // Tembak ke localhost karena akan dijalankan di terminal Windows
    const url = 'http://127.0.0.1:8000/api/v1/restaurants?per_page=15';

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        timeout: '300s',
    };

    const res = http.get(url, params);

    check(res, {
        'status is 200': (r) => r.status === 200,
        'response time < 30s': (r) => r.timings.duration < 30000,
    });
    
    // Memberikan jeda 5 detik per user agar mensimulasikan user asli dan memberi nafas pada proxy Windows
    sleep(5);
}

