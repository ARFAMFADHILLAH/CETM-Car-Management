export type CarStatus = 'tersedia' | 'tidak_tersedia' | 'di_servis';

export type Car = {
    id: number;
    nama: string;
    nomor_plat: string;
    status: CarStatus;
    foto: string | null;
    created_at: string;
    updated_at: string;
};
