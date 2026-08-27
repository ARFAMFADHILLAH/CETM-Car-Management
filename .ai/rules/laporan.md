---
paths:
  - resources/js/pages/admin/laporan/Index.vue
---

# Laporan

## Bug TS pre-existing di laporan/Index.vue (konflik nama route vs prop)
laporan/Index.vue memiliki konflik penamaan: import route (@/routes/laporan) menimpa nama prop defineProps (laporanPeminjaman/laporanMobil/laporanPengguna), sehingga vue-tsc errors TS2339 pada template. Ini bug pre-existing, belum diperbaiki. Saat menyentuh file ini, jangan perbaiki hanya 1 sub-issue; pisahkan nama import route dari nama prop agar type-check bersih.
