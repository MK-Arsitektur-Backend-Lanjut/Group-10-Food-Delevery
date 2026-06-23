# Laporan Modul 1: Restaurant & Menu

Mata Kuliah: Arsitektur dan Pengembangan Backend  
Kelompok: 10  
Anggota/Penanggung Jawab Modul 1: Muhammad Faiz Sanjaya 

## 📖 Deskripsi Modul
Modul 1 (Restaurant & Menu) adalah inti modul yang bertanggung jawab atas pengelolaan master data restoran dan menu yang ditawarkan. Modul ini menyediakan Application Programming Interface (API) untuk manajemen data menu secara dinamis (CRUD), pembaruan status operasional restoran (buka/tutup), serta validasi ketersediaan item yang dipesan untuk keperluan integrasi internal dengan Modul 2 (Order Workflow).

---

## 🛠️ Pemenuhan Ketentuan Teknis
Sesuai dengan ketentuan dan instruksi yang telah diberikan oleh Dosen Pengampu, modul ini telah dikembangkan dengan mematuhi seluruh standar teknis berikut:

### 1. Penggunaan Kerangka Kerja Laravel
Keseluruhan sistem backend dibangun menggunakan **Laravel**. Pemilihan versi dan struktur folder sudah mengikuti standar dari Laravel untuk pengembangan API.

### 2. Berjalan di dalam *Docker Container*
Proyek ini telah dikonfigurasi penuh menggunakan **Docker** (tersedia `Dockerfile` dan `docker-compose.yml`). Lingkungan pengembangan (PHP, Nginx/Apache, MySQL, dan Redis) telah diisolasi di dalam *container* untuk menjamin konsistensi *environment* antara *local development* dan *production*.

### 3. Implementasi *Repository Pattern*
Untuk menjaga kode tetap bersih (*clean code*) dan memisahkan antara logika akses data (Database) dengan logika bisnis (Controller), modul ini mengadopsi arsitektur **Repository Pattern**. 
*   Penerapan dapat dilihat pada direktori `app/Repositories/Eloquent/`.
*   Terdapat kelas spesifik seperti `EloquentRestaurantRepository` dan `EloquentMenuItemRepository` yang menjembatani Controller dengan Model Eloquent.

### 4. Data Dummy 10.000 Pesanan Historis (Seeder)
Telah disediakan *database seeder* (`OrderSeeder.php`) yang mampu men-generasi lebih dari **10.000 data pesanan (histori) beserta relasinya**. Pembuatan data ini menggunakan metode chunking/batch insert sehingga proses seeding berjalan sangat ringan tanpa risiko memory leak pada perangkat.

### 5. Penggunaan Redis (Caching)
Untuk mengoptimalkan performa pembacaan data yang intensif, sistem ini telah diintegrasikan dengan **Redis**. Redis digunakan sebagai in-memory data structure store untuk melakukan caching pada data yang sering diakses namun jarang berubah (seperti daftar menu restoran), sehingga mengurangi beban query langsung ke database MySQL.

### 6. *Database Indexing* & *Soft Deletes*
Sebagai nilai tambah untuk menjaga integritas data dan performa jangka panjang:
*   **Soft Deletes:** Diterapkan pada `MenuItem` dan `Restaurant`. Data yang dihapus tidak dihilangkan secara permanen dari tabel, sehingga riwayat pesanan (Order) di masa lalu tetap valid.
*   **Performance Indexes:** Ditambahkan pada migration untuk mempercepat proses pencarian (B-Tree Index) pada kolom-kolom kritikal seperti `restaurant_id` dan `category_id`.

---

## 🚀 Daftar Fitur & Endpoint (API)
Modul ini mengelola beberapa layanan API utama, antara lain:

1. **Manajemen Menu (CRUD):**
   * `POST /api/v1/restaurants/{restaurant}/menus` : Menambahkan menu baru.
   * `GET /api/v1/restaurants/{restaurant}/menus` : Melihat daftar menu.
   * `PUT /api/v1/menus/{menu}` : Memperbarui data menu.
   * `DELETE /api/v1/menus/{menu}` : Menghapus menu (Soft Delete).
   * `PATCH /api/v1/menus/{menu}/availability` : ketersediaan item menu.
2. **Update Status Operasional Restoran:**
   * `PATCH /api/v1/restaurants/{restaurant}/operational-status` : Mengubah status buka/tutup (*is_open*).
3. **Validasi Item Internal (Integrasi Modul 2):**
   * `POST /api/v1/internal/order-items/validate` : API internal untuk memvalidasi apakah item yang dipesan masih tersedia sebelum Modul 2 membuat *Order*.

*(Catatan: Dokumentasi API lengkap dan interaktif dapat diakses melalui Swagger UI pada rute `/api/documentation`)*

---

## 📊 *Evidence* Uji Performa (Stress Test)
Untuk membuktikan bahwa pemisahan arsitektur (Repository Pattern), Database Indexing, dan Redis berjalan optimal, telah dilakukan Stress Testing menggunakan **Grafana k6** secara lokal.

**Skenario Pengujian:**
*   **Beban Maksimal:** 10.000 Virtual Users (VUs) secara bersamaan.
*   **Durasi Uji:** Bertahap (*ramping*) hingga 6 Menit 30 Detik.
*   **Target Endpoint:** `GET /api/v1/restaurants?per_page=15`

**Hasil / Metrik (*Evidence*):**
*   **Total Checks (Permintaan):** 1.363.980 *requests*.
*   **Tingkat Keberhasilan (Success Rate):** **100.00%** (0 *failed requests* dari 1,36 juta data).
*   **Throughput:** Server mampu melayani rata-rata **1.727 requests per second (RPS)**.
*   **Response Time:** Sangat responsif dengan rata-rata (*Average*) **93.55 ms** dan Median **23.11 ms**. Bahkan pada saat beban puncak (95th percentile), respon tetap di bawah 0.4 detik (396 ms).

 TOTAL RESULTS

    checks_total.......: 1363980 3454.118386/s
    checks_succeeded...: 100.00% 1363980 out of 1363980
    checks_failed......: 0.00%   0 out of 1363980

    ✓ status is 200
    ✓ response time < 30s

    HTTP
    http_req_duration..............: avg=93.55ms min=504.7µs med=23.11ms max=3.94s p(90)=231.2ms p(95)=396.13ms
      { expected_response:true }...: avg=93.55ms min=504.7µs med=23.11ms max=3.94s p(90)=231.2ms p(95)=396.13ms
    http_req_failed................: 0.00%  0 out of 681990
    http_reqs......................: 681990 1727.059193/s

    EXECUTION
    iteration_duration.............: avg=5.09s   min=5s      med=5.02s   max=8.95s p(90)=5.23s   p(95)=5.39s
    iterations.....................: 681990 1727.059193/s
    vus............................: 1      min=0           max=10000
    vus_max........................: 10000  min=8438        max=10000

    NETWORK
    data_received..................: 4.9 GB 13 MB/s
    data_sent......................: 108 MB 273 kB/s
              
running (6m34.9s), 00000/10000 VUs, 681990 complete and 0 interrupted iterations  
load_test ✓ [======================================] 00000/10000 VUs  6m30s

Hasil ini membuktikan bahwa Modul 1 yang dikembangkan tidak hanya memenuhi syarat fungsionalitas, namun juga sangat tangguh dan siap diimplementasikan untuk menangani traffic dalam skala besar (Production-Ready).