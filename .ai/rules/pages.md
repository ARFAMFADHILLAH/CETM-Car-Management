---
paths:
  - 'resources/js/pages/*.vue'
---

# Pages

## Semua data halaman dari DB, tanpa mock & tanpa dummy
Semua halaman (Dashboard, Jadwal, Notifikasi, peminjaman, dsb) harus menampilkan data real dari database Car_Manage_CETM via Inertia props. Dilarang pakai resources/js/mock/ (sudah dihapus) atau seeder data contoh/dummy. Status labels (carStatusLabel, peminjamanStatusLabel) & tipe status ada di resources/js/lib/constants.ts. Notifikasi hanya dibuat dari aksi real approve/reject (lihat app/Services/NotifikasiService.php), tidak ada seeder notifikasi. Kalau DB kosong, halaman tampil kosong.
