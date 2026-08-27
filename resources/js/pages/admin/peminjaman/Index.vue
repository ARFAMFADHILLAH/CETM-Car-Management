<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
import {
    approve as approveRoute,
    reject as rejectRoute,
} from '@/routes/approval';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Approve Peminjaman', href: '/approve-peminjaman' },
        ],
    },
});

const props = defineProps<{
    peminjaman: {
        id: number;
        nama_peminjam: string;
        email_peminjam: string;
        no_hp: string | null;
        divisi: { nama_divisi: string } | null;
        car: { nama: string } | null;
        user: { nama: string; foto_url: string | null } | null;
        tanggal_mulai: string;
        tanggal_selesai: string;
        keperluan: string;
        lokasi_tujuan: string;
        tujuan: string;
        km_awal: number;
        km_akhir: number | null;
        tangki_bbm: string;
        nama_customer: string | null;
        catatan: string | null;
        status: PeminjamanStatus;
    }[];
}>();

const filter = ref<'semua' | PeminjamanStatus>('pending');

const pengajuan = computed(() =>
    filter.value === 'semua'
        ? props.peminjaman
        : props.peminjaman.filter((item) => item.status === filter.value),
);

const detail = ref<(typeof props.peminjaman)[number] | null>(null);

const approveForm = useForm({});
const rejectForm = useForm({});

function setujui(item: (typeof props.peminjaman)[number]): void {
    approveForm.put(approveRoute.url(item.id));
}

function tolak(item: (typeof props.peminjaman)[number]): void {
    rejectForm.put(rejectRoute.url(item.id));
}

function bukaDetail(item: (typeof props.peminjaman)[number]): void {
    detail.value = item;
}

function inisial(nama: string): string {
    return nama
        .split(' ')
        .map((w) => w.charAt(0))
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

const tangkiBbmLabel: Record<string, string> = {
    full: 'Penuh',
    '3/4': '3/4',
    '1/2': '1/2',
    '1/4': '1/4',
    empty: 'Kosong',
};

const opsiFilter: ('semua' | PeminjamanStatus)[] = [
    'semua',
    'pending',
    'disetujui',
    'ditolak',
    'selesai',
];
</script>

<template>
    <Head title="Approve Peminjaman" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Heading
                title="Approve Peminjaman"
                description="Periksa dan setujui pengajuan peminjaman mobil dari pengguna."
            />
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
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Daftar Pengajuan</CardTitle>
            </CardHeader>
            <CardContent>
                <Table v-if="pengajuan.length > 0">
                    <TableHeader>
                        <TableRow>
                            <TableHead>Peminjam</TableHead>
                            <TableHead>Mobil</TableHead>
                            <TableHead>Tanggal Pinjam</TableHead>
                            <TableHead>Tujuan</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in pengajuan" :key="item.id">
                            <TableCell>
                                <div class="flex items-center gap-3">
                                    <Avatar class="size-9">
                                        <AvatarImage
                                            v-if="item.user?.foto_url"
                                            :src="item.user.foto_url"
                                            :alt="item.nama_peminjam"
                                        />
                                        <AvatarFallback class="text-xs">
                                            {{ inisial(item.nama_peminjam) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p class="font-medium">
                                            {{ item.nama_peminjam }}
                                        </p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ item.divisi?.nama_divisi ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>{{ item.car?.nama ?? '-' }}</TableCell>
                            <TableCell class="text-sm">
                                {{ formatDateTime(item.tanggal_mulai) }}
                                <br />
                                s/d {{ formatDateTime(item.tanggal_selesai) }}
                            </TableCell>
                            <TableCell>
                                {{ item.tujuan === 'dalam_kota' ? 'Dalam Kota' : 'Luar Kota' }}
                            </TableCell>
                            <TableCell>
                                <StatusBadge :status="item.status" />
                            </TableCell>
                            <TableCell class="space-x-1 text-right">
                                <template v-if="item.status === 'pending'">
                                    <Button
                                        size="sm"
                                        :disabled="approveForm.processing"
                                        @click="setujui(item)"
                                    >
                                        Setujui
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="destructive"
                                        :disabled="rejectForm.processing"
                                        @click="tolak(item)"
                                    >
                                        Tolak
                                    </Button>
                                </template>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="bukaDetail(item)"
                                >
                                    Detail
                                </Button>
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

        <!-- Dialog detail -->
        <Dialog
            :open="detail !== null"
            @update:open="(open) => !open && (detail = null)"
        >
            <DialogContent class="sm:max-w-lg">
                <template v-if="detail">
                    <DialogHeader>
                        <DialogTitle
                            >Detail Pengajuan #{{ detail.id }}</DialogTitle
                        >
                        <DialogDescription>
                            Informasi lengkap pengajuan peminjaman.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="flex items-center gap-4 rounded-lg border p-4">
                        <Avatar class="size-14">
                            <AvatarImage
                                v-if="detail.user?.foto_url"
                                :src="detail.user.foto_url"
                                :alt="detail.nama_peminjam"
                            />
                            <AvatarFallback class="text-base">
                                {{ inisial(detail.nama_peminjam) }}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <p class="font-medium">{{ detail.nama_peminjam }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ detail.email_peminjam }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{ detail.no_hp ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <dl
                        class="grid grid-cols-[130px_1fr] gap-x-4 gap-y-2.5 text-sm"
                    >
                        <dt class="text-muted-foreground">Divisi</dt>
                        <dd>{{ detail.divisi?.nama_divisi ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Mobil</dt>
                        <dd>{{ detail.car?.nama ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Pinjam dari</dt>
                        <dd>{{ formatDateTime(detail.tanggal_mulai) }}</dd>
                        <dt class="text-muted-foreground">Pinjam sampai</dt>
                        <dd>{{ formatDateTime(detail.tanggal_selesai) }}</dd>
                        <dt class="text-muted-foreground">Tujuan</dt>
                        <dd>{{ detail.tujuan === 'dalam_kota' ? 'Dalam Kota' : 'Luar Kota' }}</dd>
                        <dt class="text-muted-foreground">Keperluan</dt>
                        <dd>{{ detail.keperluan }}</dd>
                        <dt class="text-muted-foreground">KM Awal</dt>
                        <dd>{{ detail.km_awal }}</dd>
                        <dt class="text-muted-foreground">KM Akhir</dt>
                        <dd>{{ detail.km_akhir ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Tangki BBM</dt>
                        <dd>{{ tangkiBbmLabel[detail.tangki_bbm] ?? detail.tangki_bbm }}</dd>
                        <dt class="text-muted-foreground">Lokasi tujuan</dt>
                        <dd>{{ detail.lokasi_tujuan }}</dd>
                        <dt class="text-muted-foreground">Customer</dt>
                        <dd>{{ detail.nama_customer ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Catatan</dt>
                        <dd>{{ detail.catatan ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd><StatusBadge :status="detail.status" /></dd>
                    </dl>

                    <DialogFooter>
                        <Button variant="outline" @click="detail = null">
                            Tutup
                        </Button>
                    </DialogFooter>
                </template>
            </DialogContent>
        </Dialog>
    </div>
</template>
