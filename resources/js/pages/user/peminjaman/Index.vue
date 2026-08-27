<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { peminjamanStatusLabel } from '@/lib/constants';
import type { PeminjamanStatus } from '@/lib/constants';
import { create as peminjamanCreate } from '@/routes/peminjaman';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Daftar Pengajuan', href: '/daftar-pengajuan' }],
    },
});

const props = defineProps<{
    peminjaman: {
        id: number;
        car: { nama: string } | null;
        tanggal_mulai: string;
        tanggal_selesai: string;
        keperluan: string;
        lokasi_tujuan: string;
        tujuan: string;
        status: PeminjamanStatus;
    }[];
}>();

const filter = ref<'semua' | PeminjamanStatus>('semua');

const pengajuan = computed(() =>
    filter.value === 'semua'
        ? props.peminjaman
        : props.peminjaman.filter((item) => item.status === filter.value),
);

const opsiFilter: ('semua' | PeminjamanStatus)[] = [
    'semua',
    'pending',
    'disetujui',
    'ditolak',
    'selesai',
];
</script>

<template>
    <Head title="Daftar Pengajuan" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Heading
                title="Daftar Pengajuan"
                description="Riwayat pengajuan peminjaman mobil Anda beserta statusnya."
            />
            <Button as-child>
                <Link :href="peminjamanCreate()">+ Ajukan Baru</Link>
            </Button>
        </div>

        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between gap-3"
            >
                <CardTitle>Riwayat Pengajuan</CardTitle>
                <Select v-model="filter">
                    <SelectTrigger class="w-44">
                        <SelectValue placeholder="Filter status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="opsi in opsiFilter"
                            :key="opsi"
                            :value="opsi"
                        >
                            {{
                                opsi === 'semua'
                                    ? 'Semua status'
                                    : peminjamanStatusLabel[opsi]
                            }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </CardHeader>
            <CardContent>
                <Table v-if="pengajuan.length > 0">
                    <TableHeader>
                        <TableRow>
                            <TableHead>Mobil</TableHead>
                            <TableHead>Tanggal Pinjam</TableHead>
                            <TableHead>Keperluan</TableHead>
                            <TableHead>Tujuan</TableHead>
                            <TableHead>Status</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in pengajuan" :key="item.id">
                            <TableCell class="font-medium">{{
                                item.car?.nama ?? '-'
                            }}</TableCell>
                            <TableCell class="text-sm">
                                {{ formatDateTime(item.tanggal_mulai) }}
                                <br />
                                s/d {{ formatDateTime(item.tanggal_selesai) }}
                            </TableCell>
                            <TableCell class="max-w-56 truncate">{{
                                item.keperluan
                            }}</TableCell>
                            <TableCell>
                                {{ item.tujuan === 'dalam_kota' ? 'Dalam Kota' : 'Luar Kota' }}
                            </TableCell>
                            <TableCell>
                                <StatusBadge :status="item.status" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <p
                    v-else
                    class="py-10 text-center text-sm text-muted-foreground"
                >
                    Tidak ada pengajuan pada filter ini.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
