<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CalendarDays, Car, ClipboardCheck, Clock3 } from '@lucide/vue';
import { computed } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDateTime } from '@/lib/format';
import type { PeminjamanStatus } from '@/lib/constants';
import { dashboard } from '@/routes';
import { index as approval } from '@/routes/approval';
import { approve as approveRoute, reject as rejectRoute } from '@/routes/approval';
import { create as peminjamanCreate } from '@/routes/peminjaman';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

type PeminjamanItem = {
    id: number;
    nama_peminjam: string;
    divisi: { nama_divisi: string } | null;
    car: { nama: string } | null;
    tanggal_mulai: string;
    tanggal_selesai: string;
    keperluan: string;
    lokasi_tujuan: string;
    status: PeminjamanStatus;
};

const props = defineProps<{
    totalMobil: number;
    mobilTersedia: number;
    mobilDiservis: number;
    pengajuanPending: PeminjamanItem[];
    jadwalTerdekat: PeminjamanItem[];
    isAdmin: boolean;
}>();

const setujuiForm = useForm({});
const tolakForm = useForm({});

function setujui(item: PeminjamanItem): void {
    setujuiForm.put(approveRoute.url(item.id));
}

function tolak(item: PeminjamanItem): void {
    tolakForm.put(rejectRoute.url(item.id));
}

const jadwalTerdekat = computed(() => props.jadwalTerdekat);
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div>
            <h1 class="text-xl font-semibold tracking-tight">
                Selamat datang
            </h1>
            <p class="text-sm text-muted-foreground">
                {{
                    isAdmin
                        ? 'Pantau aktivitas peminjaman dan kelola armada dari sini.'
                        : 'Ajukan peminjaman mobil dan pantau jadwalnya dari sini.'
                }}
            </p>
        </div>

        <!-- Kartu statistik -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium"
                        >Mobil Tersedia</CardTitle
                    >
                    <Car class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold">{{ mobilTersedia }}</p>
                    <p class="text-xs text-muted-foreground">
                        dari {{ totalMobil }} mobil
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium">
                        {{
                            isAdmin ? 'Menunggu Persetujuan' : 'Mobil Di Servis'
                        }}
                    </CardTitle>
                    <ClipboardCheck
                        v-if="isAdmin"
                        class="size-4 text-muted-foreground"
                    />
                    <Car v-else class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <p v-if="isAdmin" class="text-3xl font-bold">
                        {{ pengajuanPending.length }}
                    </p>
                    <template v-else>
                        <p class="text-3xl font-bold">{{ mobilDiservis }}</p>
                        <p class="text-xs text-muted-foreground">
                            di servis / tidak tersedia
                        </p>
                    </template>
                </CardContent>
            </Card>

            <Card>
                <CardHeader
                    class="flex flex-row items-center justify-between pb-2"
                >
                    <CardTitle class="text-sm font-medium"
                        >Jadwal Terdekat</CardTitle
                    >
                    <CalendarDays class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold">
                        {{ jadwalTerdekat.length }}
                    </p>
                    <p class="text-xs text-muted-foreground">
                        peminjaman terjadwal
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Konten per role -->
        <template v-if="isAdmin">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Pengajuan Menunggu Persetujuan</CardTitle>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Pengajuan terbaru yang perlu ditindaklanjuti.
                        </p>
                    </div>
                    <Button as-child variant="outline" size="sm">
                        <Link :href="approval()">Lihat semua</Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <Table v-if="pengajuanPending.length > 0">
                        <TableHeader>
                            <TableRow>
                                <TableHead>Peminjam</TableHead>
                                <TableHead>Mobil</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Kegiatan</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="item in pengajuanPending"
                                :key="item.id"
                            >
                                <TableCell>
                                    <p class="font-medium">
                                        {{ item.nama_peminjam }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ item.divisi?.nama_divisi ?? '-' }}
                                    </p>
                                </TableCell>
                                <TableCell>{{ item.car?.nama ?? '-' }}</TableCell>
                                <TableCell class="text-sm">
                                    {{ formatDateTime(item.tanggal_mulai) }}
                                    <br />
                                    s/d
                                    {{ formatDateTime(item.tanggal_selesai) }}
                                </TableCell>
                                <TableCell class="max-w-48 truncate">{{
                                    item.keperluan
                                }}</TableCell>
                                <TableCell>
                                    <StatusBadge :status="item.status" />
                                </TableCell>
                                <TableCell class="space-x-1 text-right">
                                    <Button
                                        size="sm"
                                        :disabled="setujuiForm.processing"
                                        @click="setujui(item)"
                                        >Setujui</Button
                                    >
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        :disabled="tolakForm.processing"
                                        @click="tolak(item)"
                                    >
                                        Tolak
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                    <p
                        v-else
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        Semua pengajuan sudah ditindaklanjuti.
                    </p>
                </CardContent>
            </Card>
        </template>

        <template v-else>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Jadwal Peminjaman Terdekat</CardTitle>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Daftar peminjaman Anda yang telah disetujui.
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Button as-child size="sm">
                            <Link :href="peminjamanCreate()"
                                >+ Ajukan Peminjaman</Link
                            >
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="flex flex-col gap-3">
                    <div
                        v-for="item in jadwalTerdekat"
                        :key="item.id"
                        class="flex items-start justify-between gap-4 rounded-lg border p-3"
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="flex size-9 items-center justify-center rounded-md bg-primary/10 text-primary"
                            >
                                <Clock3 class="size-4" />
                            </span>
                            <div>
                                <p class="text-sm font-medium">
                                    {{ item.car?.nama ?? '-' }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatDateTime(item.tanggal_mulai) }} —
                                    {{ formatDateTime(item.tanggal_selesai) }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ item.keperluan }} ·
                                    {{ item.lokasi_tujuan }}
                                </p>
                            </div>
                        </div>
                        <Badge variant="outline">{{
                            item.divisi?.nama_divisi ?? '-'
                        }}</Badge>
                    </div>

                    <p
                        v-if="jadwalTerdekat.length === 0"
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        Belum ada jadwal peminjaman yang disetujui.
                    </p>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
