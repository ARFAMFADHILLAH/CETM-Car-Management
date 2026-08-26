export type MockNotifikasi = {
    id: number;
    judul: string;
    pesan: string;
    waktu: string;
    dibaca: boolean;
    tipe: 'disetujui' | 'ditolak' | 'pengingat' | 'info';
};

function ago(hours: number): string {
    const date = new Date();
    date.setHours(date.getHours() - hours);

    return date.toISOString();
}

export const mockNotifikasi: MockNotifikasi[] = [
    {
        id: 1,
        judul: 'Pengajuan disetujui',
        pesan: 'Pengajuan peminjaman Toyota Avanza (Bandung) telah disetujui admin.',
        waktu: ago(2),
        dibaca: false,
        tipe: 'disetujui',
    },
    {
        id: 2,
        judul: 'Pengajuan ditolak',
        pesan: 'Pengajuan peminjaman Toyota Hiace (Surabaya) ditolak. Catatan: mobil sedang di servis.',
        waktu: ago(26),
        dibaca: false,
        tipe: 'ditolak',
    },
    {
        id: 3,
        judul: 'Pengingat jadwal',
        pesan: 'Peminjaman Isuzu Elf dimulai besok pukul 08.00. Jangan lupa ambil kunci di kantor.',
        waktu: ago(30),
        dibaca: true,
        tipe: 'pengingat',
    },
    {
        id: 4,
        judul: 'Mobil kembali dari servis',
        pesan: 'Toyota Hiace telah selesai diservis dan siap digunakan kembali.',
        waktu: ago(72),
        dibaca: true,
        tipe: 'info',
    },
];
