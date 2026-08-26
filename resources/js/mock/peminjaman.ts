export type PeminjamanStatus = 'pending' | 'disetujui' | 'ditolak' | 'selesai';

export type MockPeminjaman = {
    id: number;
    nama_peminjam: string;
    email_peminjam: string;
    no_hp: string;
    divisi: string;
    car_id: number;
    car_nama: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    kegiatan: string;
    lokasi_tujuan: string;
    nama_customer: string | null;
    catatan: string | null;
    status: PeminjamanStatus;
};

function at(dayOffset: number, hour: number): string {
    const date = new Date();
    date.setDate(date.getDate() + dayOffset);
    date.setHours(hour, 0, 0, 0);

    return date.toISOString();
}

export const peminjamanStatusLabel: Record<PeminjamanStatus, string> = {
    pending: 'Menunggu',
    disetujui: 'Disetujui',
    ditolak: 'Ditolak',
    selesai: 'Selesai',
};

export const mockPeminjaman: MockPeminjaman[] = [
    {
        id: 1,
        nama_peminjam: 'Budi Santoso',
        email_peminjam: 'budi@example.com',
        no_hp: '081200000001',
        divisi: 'Operasional',
        car_id: 1,
        car_nama: 'Toyota Avanza',
        tanggal_mulai: at(1, 8),
        tanggal_selesai: at(2, 17),
        kegiatan: 'Kunjungan customer PT Sinar Jaya',
        lokasi_tujuan: 'Bandung',
        nama_customer: 'PT Sinar Jaya',
        catatan: 'Bawa dokumen kontrak.',
        status: 'disetujui',
    },
    {
        id: 2,
        nama_peminjam: 'Siti Rahayu',
        email_peminjam: 'siti@example.com',
        no_hp: '081200000002',
        divisi: 'Keuangan',
        car_id: 4,
        car_nama: 'Mitsubishi Xpander',
        tanggal_mulai: at(3, 9),
        tanggal_selesai: at(4, 16),
        kegiatan: 'Survey lokasi gudang baru',
        lokasi_tujuan: 'Bekasi',
        nama_customer: null,
        catatan: null,
        status: 'pending',
    },
    {
        id: 3,
        nama_peminjam: 'Andi Wijaya',
        email_peminjam: 'andi@example.com',
        no_hp: '081200000003',
        divisi: 'Pemasaran',
        car_id: 2,
        car_nama: 'Toyota Kijang Innova',
        tanggal_mulai: at(6, 7),
        tanggal_selesai: at(8, 18),
        kegiatan: 'Rapat kontrak kerja sama',
        lokasi_tujuan: 'Semarang',
        nama_customer: 'CV Mitra Usaha',
        catatan: null,
        status: 'pending',
    },
    {
        id: 4,
        nama_peminjam: 'Rina Marlina',
        email_peminjam: 'rina@example.com',
        no_hp: '081200000004',
        divisi: 'Logistik',
        car_id: 6,
        car_nama: 'Isuzu Elf',
        tanggal_mulai: at(-4, 8),
        tanggal_selesai: at(-2, 17),
        kegiatan: 'Pengiriman dokumen penting',
        lokasi_tujuan: 'Jakarta Selatan',
        nama_customer: null,
        catatan: 'Perjalanan sudah selesai.',
        status: 'selesai',
    },
    {
        id: 5,
        nama_peminjam: 'Dedi Kurniawan',
        email_peminjam: 'dedi@example.com',
        no_hp: '081200000005',
        divisi: 'Teknik',
        car_id: 3,
        car_nama: 'Toyota Hiace',
        tanggal_mulai: at(-1, 6),
        tanggal_selesai: at(0, 20),
        kegiatan: 'Dinas luar kota - audit cabang',
        lokasi_tujuan: 'Surabaya',
        nama_customer: null,
        catatan: null,
        status: 'ditolak',
    },
];
