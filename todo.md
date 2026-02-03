
- [ ] import siswa
- [ ] export siswa
- [ ] create invoice buku
- [x] create invoice spp
- [ ] export invoice buku
- [ ] export invoice spp
- [ ] import pembayaran buku
- [ ] import pembayaran spp
- [ ] fitur penjadwalan pembuatan tagihan
- [ ] tampilkan siswa yang masih nunggak dan udh lulus/keluar

- [ ] panel migrasi dari siswa dan semua tagihan
- [ ] buat page/halaman pre use, jadi semisal blm ada tahun ajaran aktif/semester aktif, user akan di redirect kesini, spya memastikan tahun ajaran dan semester tetap ada sblm mulai menggunakan
- [ ] admin hanya bisa edit 1 data student terakhir, yang lain harus menjadi history
- [ ] saat gagal membuat invoice, anak yang gagal beserta keterangan gagal masukkan ke database notifikasi, buat pages dan tampilkan sebagai tabel
- [ ] g bisa delete first payment method di student form
- [ ] buat middleware untiuk ngecek apakah aplikasi sudah siap digunakan,. semisal tahun ajaran/semester atau apapun blm siap diguanakn maka dia akan masuk ke halaman tertentu. 


- definisikan arti siswa aktif
    - school_id tidak null berakrti masih aktif
    - jika school_id
    - status = active
- menjaga siswa aktif tetap konsisten pakai tabel student_enrollment
    - tahun ajaran aktif
    - semester aktif
    - status -> ENROLLED

- definisi student g aktif
    - statusnya non aktif 
    - school_id di tabel student null

- cra menonaktifkan siswa
    - saat naik kelas
    - pakai tombol siswa pindah ke luar
    - pakai tombol siswa drop out

Semua history akan tersimpan di student enrollment sebagai source of truth, yg ada di tabel student itu hasil dari enrollment, nah jadi kedua proses ini harus lakukan di dalam db transactional

- definisikan arti payment account aktif dari student
    - ambil school_id kmudian ambil semua classroomid dari relasi chool ke classroom, kmdian stlah mendapatkan classroom id nya
    - ambil student enrollment yg aktif, ambil classroom_id nya kmduian cocokkan semua classrooms id dari school dan classroom_id yg aktif dari student enrollment

- definisikan arti buat tagihan spp hanya untuk siswa aktif
    - ambil semua school id berdasarkan classrom yg masuk kategori aktif di tabel enrollment,
    - hasil dari school id ambil semua payment account berdasarkan schoolid, kmudian baru ambil siswanya


- siapa aja yang bisa dibuatkan invoice?
    - siswa aktif tahun ajaran ini

setiap siswa yang dibuatkan invoice nomor virtual account invoice itu harus di update juga seperti taggal


pas export tagihan baru user admin bisa milih yg nunggak mau bayar berapa bulan dulu klo emng minta keringanan