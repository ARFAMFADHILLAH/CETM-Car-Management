export type MockPengguna = {
    id: number;
    nama: string;
    email: string;
    no_hp: string | null;
    divisi: string | null;
    role: 'admin' | 'user';
    email_verified_at: string | null;
};

export const mockDivisi = [
    'Operasional',
    'Keuangan',
    'Sumber Daya Manusia',
    'Pemasaran',
    'Teknik',
    'Logistik',
] as const;

export const mockPengguna: MockPengguna[] = [
    {
        id: 1,
        nama: 'Administrator',
        email: 'admin@example.com',
        no_hp: '081234567890',
        divisi: null,
        role: 'admin',
        email_verified_at: '2026-08-01T00:00:00Z',
    },
    {
        id: 2,
        nama: 'Budi Santoso',
        email: 'budi@example.com',
        no_hp: '081200000001',
        divisi: 'Operasional',
        role: 'user',
        email_verified_at: '2026-08-02T00:00:00Z',
    },
    {
        id: 3,
        nama: 'Siti Rahayu',
        email: 'siti@example.com',
        no_hp: '081200000002',
        divisi: 'Keuangan',
        role: 'user',
        email_verified_at: '2026-08-03T00:00:00Z',
    },
    {
        id: 4,
        nama: 'Andi Wijaya',
        email: 'andi@example.com',
        no_hp: '081200000003',
        divisi: 'Pemasaran',
        role: 'user',
        email_verified_at: null,
    },
];

export const mockAdminTambahan: MockPengguna[] = [
    {
        id: 5,
        nama: 'Ratna Dewi',
        email: 'ratna.admin@example.com',
        no_hp: '081300000001',
        divisi: null,
        role: 'admin',
        email_verified_at: '2026-08-05T00:00:00Z',
    },
];
