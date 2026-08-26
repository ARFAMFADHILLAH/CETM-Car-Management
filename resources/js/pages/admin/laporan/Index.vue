<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Download, Printer } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDateTime } from '@/lib/format';
import {
    peminjaman as laporanPeminjaman,
    mobil as laporanMobil,
    pengguna as laporanPengguna,
} from '@/routes/laporan';
import {
    pdf as exportPdf,
    excel as exportExcel,
} from '@/routes/laporan/export';

type Tab = 'peminjaman' | 'mobil' | 'pengguna';

interface LaporanPeminjaman {
    id: number;
    nama_peminjam: string;
    email_peminjam: string;
    no_hp: string | null;
    divisi: { nama_divisi: string } | null;
    car: { nama: string } | null;
    tanggal_mulai: string;
    tanggal_selesai: string;
    kegiatan: string;
    lokasi_tujuan: string;
    nama_customer: string | null;
    status: { value: string };
}

interface LaporanMobil {
    id: number;
    nama: string;
    nomor_plat: string;
    status: { value: string };
    peminjaman_count: number;
}

interface LaporanPengguna {
    id: number;
    nama: string;
    email: string;
    no_hp: string | null;
    jumlah_peminjaman: number;
}

interface DivisiOption {
    id: number;
    nama_divisi: string;
}

const props = defineProps<{
    tab?: Tab;
    laporanPeminjaman?: LaporanPeminjaman[];
    laporanMobil?: LaporanMobil[];
    laporanPengguna?: LaporanPengguna[];
    divisiList?: DivisiOption[];
    filters?: Record<string, string>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Laporan', href: '/laporan' }],
    },
});

const activeTab = ref<Tab>(props.tab ?? 'peminjaman');

const filterTanggalMulai = ref(props.filters?.tanggal_mulai ?? '');
const filterTanggalSelesai = ref(props.filters?.tanggal_selesai ?? '');
const filterStatus = ref(props.filters?.status ?? 'semua');
const filterDivisiId = ref(props.filters?.divisi_id ?? '');

const opsiStatus = [
    { value: 'semua', label: 'Semua Status' },
    { value: 'pending', label: 'Menunggu' },
    { value: 'disetujui', label: 'Disetujui' },
    { value: 'ditolak', label: 'Ditolak' },
    { value: 'selesai', label: 'Selesai' },
];

function gantiTab(tab: Tab): void {
    activeTab.value = tab;

    if (tab === 'peminjaman') {
        router.get(laporanPeminjaman.url(), {}, { preserveState: true });
    } else if (tab === 'mobil') {
        router.get(laporanMobil.url(), {}, { preserveState: true });
    } else {
        router.get(laporanPengguna.url(), {}, { preserveState: true });
    }
}

function terapkanFilter(): void {
    const params: Record<string, string> = {};

    if (filterTanggalMulai.value) params.tanggal_mulai = filterTanggalMulai.value;
    if (filterTanggalSelesai.value) params.tanggal_selesai = filterTanggalSelesai.value;
    if (filterStatus.value !== 'semua') params.status = filterStatus.value;
    if (filterDivisiId.value) params.divisi_id = filterDivisiId.value;

    router.get(laporanPeminjaman.url(), params, { preserveState: true });
}

function resetFilter(): void {
    filterTanggalMulai.value = '';
    filterTanggalSelesai.value = '';
    filterStatus.value = 'semua';
    filterDivisiId.value = '';
    router.get(laporanPeminjaman.url(), {}, { preserveState: true });
}

function getExportUrl(type: Tab, format: 'pdf' | 'excel'): string {
    const base =
        format === 'pdf'
            ? exportPdf.url(type)
            : exportExcel.url(type);

    if (type !== 'peminjaman') {
        return base;
    }

    const params: Record<string, string> = {};
    if (filterTanggalMulai.value) params.tanggal_mulai = filterTanggalMulai.value;
    if (filterTanggalSelesai.value) params.tanggal_selesai = filterTanggalSelesai.value;
    if (filterStatus.value !== 'semua') params.status = filterStatus.value;
    if (filterDivisiId.value) params.divisi_id = filterDivisiId.value;

    const qs = new URLSearchParams(params).toString();
    return qs ? `${base}?${qs}` : base;
}
</script>

<template>
    <Head title="Laporan" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Heading
                title="Laporan"
                description="Lihat dan cetak laporan data peminjaman, mobil, dan pengguna."
            />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <a :href="getExportUrl(activeTab, 'pdf')" target="_blank">
                        <Printer class="mr-1 size-4" />
                        Cetak PDF
                    </a>
                </Button>
                <Button variant="outline" as-child>
                    <a :href="getExportUrl(activeTab, 'excel')" target="_blank">
                        <Download class="mr-1 size-4" />
                        Export Excel
                    </a>
                </Button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-1 rounded-lg bg-muted p-1">
            <button
                v-for="tab in [
                    { key: 'peminjaman', label: 'Peminjaman' },
                    { key: 'mobil', label: 'Mobil' },
                    { key: 'pengguna', label: 'Pengguna' },
                ] as const"
                :key="tab.key"
                class="flex-1 rounded-md px-3 py-2 text-sm font-medium transition-colors"
                :class="
                    activeTab === tab.key
                        ? 'bg-background text-foreground shadow-sm'
                        : 'text-muted-foreground hover:text-foreground'
                "
                @click="gantiTab(tab.key)"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- Tab: Peminjaman -->
        <template v-if="activeTab === 'peminjaman'">
            <!-- Filter Bar -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Filter</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="grid gap-2">
                            <Label for="filter-tgl-mulai">Tanggal Mulai</Label>
                            <Input
                                id="filter-tgl-mulai"
                                v-model="filterTanggalMulai"
                                type="date"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="filter-tgl-selesai">Tanggal Selesai</Label>
                            <Input
                                id="filter-tgl-selesai"
                                v-model="filterTanggalSelesai"
                                type="date"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label>Status</Label>
                            <Select v-model="filterStatus">
                                <SelectTrigger class="w-full">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opsi in opsiStatus"
                                        :key="opsi.value"
                                        :value="opsi.value"
                                    >
                                        {{ opsi.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label>Divisi</Label>
                            <Select v-model="filterDivisiId">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Semua divisi" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Semua Divisi</SelectItem>
                                    <SelectItem
                                        v-for="d in divisiList"
                                        :key="d.id"
                                        :value="String(d.id)"
                                    >
                                        {{ d.nama_divisi }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <Button size="sm" @click="terapkanFilter">
                            Terapkan
                        </Button>
                        <Button size="sm" variant="outline" @click="resetFilter">
                            Reset
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Tabel Peminjaman -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">
                        Data Peminjaman
                        <span class="text-muted-foreground">
                            ({{ laporanPeminjaman?.length ?? 0 }} data)
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Table v-if="laporanPeminjaman && laporanPeminjaman.length > 0">
                        <TableHeader>
                            <TableRow>
                                <TableHead>No</TableHead>
                                <TableHead>Peminjam</TableHead>
                                <TableHead>Mobil</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Kegiatan</TableHead>
                                <TableHead>Lokasi</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in laporanPeminjaman" :key="item.id">
                                <TableCell>{{ item.id }}</TableCell>
                                <TableCell>
                                    <p class="font-medium">{{ item.nama_peminjam }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.divisi?.nama_divisi ?? '-' }}
                                    </p>
                                </TableCell>
                                <TableCell>{{ item.car?.nama ?? '-' }}</TableCell>
                                <TableCell class="text-sm">
                                    {{ formatDateTime(item.tanggal_mulai) }}
                                    <br />
                                    s/d {{ formatDateTime(item.tanggal_selesai) }}
                                </TableCell>
                                <TableCell>{{ item.kegiatan }}</TableCell>
                                <TableCell>{{ item.lokasi_tujuan }}</TableCell>
                                <TableCell>
                                    <StatusBadge :status="item.status.value" />
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <p v-else class="py-10 text-center text-sm text-muted-foreground">
                        Tidak ada data peminjaman.
                    </p>
                </CardContent>
            </Card>
        </template>

        <!-- Tab: Mobil -->
        <template v-if="activeTab === 'mobil'">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">
                        Data Mobil
                        <span class="text-muted-foreground">
                            ({{ laporanMobil?.length ?? 0 }} unit)
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Table v-if="laporanMobil && laporanMobil.length > 0">
                        <TableHeader>
                            <TableRow>
                                <TableHead>No</TableHead>
                                <TableHead>Nama Mobil</TableHead>
                                <TableHead>Nomor Plat</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Jumlah Peminjaman</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in laporanMobil" :key="item.id">
                                <TableCell>{{ item.id }}</TableCell>
                                <TableCell class="font-medium">{{ item.nama }}</TableCell>
                                <TableCell class="font-mono">{{ item.nomor_plat }}</TableCell>
                                <TableCell>
                                    <StatusBadge :status="item.status.value" />
                                </TableCell>
                                <TableCell class="text-right">
                                    {{ item.peminjaman_count }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <p v-else class="py-10 text-center text-sm text-muted-foreground">
                        Tidak ada data mobil.
                    </p>
                </CardContent>
            </Card>
        </template>

        <!-- Tab: Pengguna -->
        <template v-if="activeTab === 'pengguna'">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">
                        Data Pengguna
                        <span class="text-muted-foreground">
                            ({{ laporanPengguna?.length ?? 0 }} orang)
                        </span>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Table v-if="laporanPengguna && laporanPengguna.length > 0">
                        <TableHeader>
                            <TableRow>
                                <TableHead>No</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>No. HP</TableHead>
                                <TableHead class="text-right">Jumlah Peminjaman</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in laporanPengguna" :key="item.id">
                                <TableCell>{{ item.id }}</TableCell>
                                <TableCell class="font-medium">{{ item.nama }}</TableCell>
                                <TableCell>{{ item.email }}</TableCell>
                                <TableCell>{{ item.no_hp ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    {{ item.jumlah_peminjaman }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <p v-else class="py-10 text-center text-sm text-muted-foreground">
                        Tidak ada data pengguna.
                    </p>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
