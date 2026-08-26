export type CarStatus = 'tersedia' | 'tidak_tersedia' | 'di_servis';

export type MockCar = {
    id: number;
    nama: string;
    nomor_plat: string;
    status: CarStatus;
};

export const mockCars: MockCar[] = [
    {
        id: 1,
        nama: 'Toyota Avanza',
        nomor_plat: 'B 1234 XYZ',
        status: 'tersedia',
    },
    {
        id: 2,
        nama: 'Toyota Kijang Innova',
        nomor_plat: 'B 2345 ABC',
        status: 'tersedia',
    },
    {
        id: 3,
        nama: 'Toyota Hiace',
        nomor_plat: 'B 7512 TRK',
        status: 'di_servis',
    },
    {
        id: 4,
        nama: 'Mitsubishi Xpander',
        nomor_plat: 'D 4567 KLM',
        status: 'tersedia',
    },
    {
        id: 5,
        nama: 'Suzuki Ertiga',
        nomor_plat: 'L 8901 MNO',
        status: 'tidak_tersedia',
    },
    { id: 6, nama: 'Isuzu Elf', nomor_plat: 'B 3377 GHI', status: 'tersedia' },
];

export const carStatusLabel: Record<CarStatus, string> = {
    tersedia: 'Tersedia',
    tidak_tersedia: 'Tidak Tersedia',
    di_servis: 'Di Servis',
};
