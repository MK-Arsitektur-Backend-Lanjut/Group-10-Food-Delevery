import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    // Skenario: Melakukan total 10.000 hit (iterations) dengan 1.000 Virtual Users (VU)
    // yang menyerang secara berbarengan (concurrently) secepat mungkin.
    scenarios: {
        shared_iter_scenario: {
            executor: 'shared-iterations',
            vus: 1000,          // 1.000 pengguna bersamaan
            iterations: 10000,  // Target total 10.000 hit/request
            maxDuration: '3m',  // Batas waktu maksimal test berjalan (3 menit)
        },
    },
};

export default function () {
    // 1. Tentukan URL endpoint yang ingin dites. 
    // Ganti endpoint ini sesuai yang dosen Anda minta.
    // Contoh di bawah ini mencoba hit pagination standard:
    const url = 'http://127.0.0.1:8000/api/v1/restaurants?per_page=15';

    // Jika ingin mencoba endpoint bulk data 10.000 (HATI-HATI MEMORI SERVER BISA PENUH!):
    // const url = 'http://127.0.0.1:8000/api/v1/restaurants/all?per_page=1000';

    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            // 'Authorization': 'Bearer YOUR_TOKEN_HERE', // Hapus komentar ini jika endpoint butuh login
        },
    };

    const res = http.get(url, params);

    // 2. Mengecek apakah responsnya berhasil (HTTP 200)
    check(res, {
        'status is 200': (r) => r.status === 200,
        // Cek apakah response lebih cepat dari 1 detik (1000ms)
        'response time < 1000ms': (r) => r.timings.duration < 1000, 
    });
    
    // Memberikan jeda sangat kecil antar request (opsional)
    // sleep(0.01); 
}
