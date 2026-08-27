export type CarStatus = 'tersedia' | 'tidak_tersedia' | 'di_servis';

export type PeminjamanStatus = 'pending' | 'disetujui' | 'ditolak' | 'selesai';

export type NotifikasiTipe = 'disetujui' | 'ditolak' | 'pengingat' | 'info';

export const carStatusLabel: Record<CarStatus, string> = {
    tersedia: 'Tersedia',
    tidak_tersedia: 'Tidak Tersedia',
    di_servis: 'Di Servis',
};

export const peminjamanStatusLabel: Record<PeminjamanStatus, string> = {
    pending: 'Menunggu',
    disetujui: 'Disetujui',
    ditolak: 'Ditolak',
    selesai: 'Selesai',
};
