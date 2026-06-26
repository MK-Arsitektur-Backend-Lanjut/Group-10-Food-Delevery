Dokumentasi Modul User & Driver
Cakupan Modul

Autentikasi User & Driver (JWT)
Pencarian Driver yang Tersedia
Riwayat Pengantaran
1. Autentikasi User & Driver (JWT)
Sistem menggunakan JSON Web Token (JWT) sebagai metode autentikasi agar pengguna dapat mengakses aplikasi dengan aman.

Proses Login Saat User atau Driver memasukkan email dan password yang benar, sistem akan membuat token JWT. Token ini menjadi tanda bahwa pengguna telah berhasil login.

Akses ke API Setiap kali User atau Driver mengakses endpoint API, token JWT harus dikirim melalui Authorization Header dengan format: Bearer {token} Sistem akan memeriksa token tersebut sebelum memproses permintaan.

Pembatasan Hak Akses User dan Driver memiliki hak akses yang berbeda. Karena itu, sistem memastikan bahwa:

User hanya dapat mengakses fitur yang memang diperuntukkan bagi User.
Driver hanya dapat mengakses fitur yang berkaitan dengan Driver.
Dengan cara ini, setiap pengguna hanya bisa menggunakan fitur sesuai perannya.

2. Sistem Pencarian Driver yang Tersedia
Saat pelanggan membuat pesanan, sistem akan mencari driver yang sedang siap menerima order.

Cara Kerja Proses pencarian dilakukan melalui fungsi getAvailableDrivers() yang terdapat pada DriverRepository. Fungsi ini hanya mengambil data driver yang memiliki status available, sehingga hanya driver yang sedang tersedia yang akan ditampilkan.

Optimasi Pencarian Karena proses ini dilakukan sangat sering, kolom status pada tabel drivers telah diberikan database index. Dengan adanya index, pencarian driver tetap cepat meskipun jumlah driver di dalam database terus bertambah.

3. Pencatatan Riwayat Pengantaran
Setiap aktivitas pengantaran akan disimpan sebagai riwayat agar dapat digunakan untuk pelacakan, evaluasi, maupun kebutuhan administrasi.

Penyimpanan Data Riwayat pengantaran disimpan pada tabel DeliveryHistory, yang memiliki hubungan dengan data Driver dan Order.

Melihat Riwayat Driver Fungsi getDriverHistory() digunakan untuk mengambil seluruh riwayat pengantaran berdasarkan ID Driver. Fungsi ini akan menampilkan daftar pesanan yang pernah atau sedang dikerjakan oleh driver tersebut.

Optimasi Pengambilan Data Saat mengambil riwayat pengantaran, sistem menggunakan Eager Loading (with('order')). Teknik ini memungkinkan data pesanan diambil sekaligus dalam satu proses sehingga jumlah query ke database menjadi lebih sedikit dan performa aplikasi tetap optimal.

