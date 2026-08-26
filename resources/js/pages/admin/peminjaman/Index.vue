<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import StatusBadge from '@/components/StatusBadge.vue';
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
import { mockPeminjaman, peminjamanStatusLabel } from '@/mock/peminjaman';
import type { MockPeminjaman, PeminjamanStatus } from '@/mock/peminjaman';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Approve Peminjaman', href: '/approve-peminjaman' },
        ],
    },
});

const filter = ref<'semua' | PeminjamanStatus>('pending');

const pengajuan = computed(() =>
    filter.value === 'semua'
        ? mockPeminjaman
        : mockPeminjaman.filter((item) => item.status === filter.value),
);

const detail = ref<MockPeminjaman | null>(null);

function bukaDetail(item: MockPeminjaman): void {
    detail.value = item;
}

function setujui(item: MockPeminjaman): void {
    item.status = 'disetujui';
    detail.value = null;
    toast.success(`Pengajuan ${item.nama_peminjam} disetujui.`);
}

function tolak(item: MockPeminjaman): void {
    item.status = 'ditolak';
    detail.value = null;
    toast.info(`Pengajuan ${item.nama_peminjam} ditolak.`);
}

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
                                <p class="font-medium">
                                    {{ item.nama_peminjam }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ item.divisi }}
                                </p>
                            </TableCell>
                            <TableCell>{{ item.car_nama }}</TableCell>
                            <TableCell class="text-sm">
                                {{ formatDateTime(item.tanggal_mulai) }}
                                <br />
                                s/d {{ formatDateTime(item.tanggal_selesai) }}
                            </TableCell>
                            <TableCell>{{ item.lokasi_tujuan }}</TableCell>
                            <TableCell>
                                <StatusBadge :status="item.status" />
                            </TableCell>
                            <TableCell class="space-x-1 text-right">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="bukaDetail(item)"
                                >
                                    Detail
                                </Button>
                                <Button
                                    v-if="item.status === 'pending'"
                                    size="sm"
                                    @click="setujui(item)"
                                >
                                    Setujui
                                </Button>
                                <Button
                                    v-if="item.status === 'pending'"
                                    size="sm"
                                    variant="destructive"
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

                    <dl
                        class="grid grid-cols-[130px_1fr] gap-x-4 gap-y-2.5 text-sm"
                    >
                        <dt class="text-muted-foreground">Peminjam</dt>
                        <dd class="font-medium">{{ detail.nama_peminjam }}</dd>
                        <dt class="text-muted-foreground">Email</dt>
                        <dd>{{ detail.email_peminjam }}</dd>
                        <dt class="text-muted-foreground">No. HP</dt>
                        <dd>{{ detail.no_hp ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Divisi</dt>
                        <dd>{{ detail.divisi }}</dd>
                        <dt class="text-muted-foreground">Mobil</dt>
                        <dd>{{ detail.car_nama }}</dd>
                        <dt class="text-muted-foreground">Pinjam dari</dt>
                        <dd>{{ formatDateTime(detail.tanggal_mulai) }}</dd>
                        <dt class="text-muted-foreground">Pinjam sampai</dt>
                        <dd>{{ formatDateTime(detail.tanggal_selesai) }}</dd>
                        <dt class="text-muted-foreground">Kegiatan</dt>
                        <dd>{{ detail.kegiatan }}</dd>
                        <dt class="text-muted-foreground">Lokasi tujuan</dt>
                        <dd>{{ detail.lokasi_tujuan }}</dd>
                        <dt class="text-muted-foreground">Customer</dt>
                        <dd>{{ detail.nama_customer ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Catatan</dt>
                        <dd>{{ detail.catatan ?? '-' }}</dd>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd><StatusBadge :status="detail.status" /></dd>
                    </dl>

                    <DialogFooter class="gap-2 sm:gap-0">
                        <Button variant="outline" @click="detail = null"
                            >Tutup</Button
                        >
                        <Button
                            v-if="detail.status === 'pending'"
                            @click="setujui(detail)"
                        >
                            Setujui
                        </Button>
                        <Button
                            v-if="detail.status === 'pending'"
                            variant="destructive"
                            @click="tolak(detail)"
                        >
                            Tolak
                        </Button>
                    </DialogFooter>
                </template>
            </DialogContent>
        </Dialog>
    </div>
</template>
