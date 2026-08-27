<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import MonthCalendar from '@/components/MonthCalendar.vue';
import type { CalendarEvent } from '@/components/MonthCalendar.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatShortDate, formatTime } from '@/lib/format';
import { mockPeminjaman } from '@/mock/peminjaman';
import type { MockPeminjaman } from '@/mock/peminjaman';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Jadwal Mobil', href: '/jadwal-mobil' }],
    },
});

// Ekspansi tiap peminjaman menjadi acara harian untuk kalender...
const events = computed<CalendarEvent[]>(() => {
    const list: CalendarEvent[] = [];

    for (const item of mockPeminjaman) {
        if (item.status !== 'disetujui' && item.status !== 'selesai') {
            continue;
        }

        const mulai = new Date(item.tanggal_mulai);
        const selesai = new Date(item.tanggal_selesai);
        selesai.setHours(23, 59, 59, 0);

        for (
            let hari = new Date(mulai);
            hari <= selesai;
            hari.setDate(hari.getDate() + 1)
        ) {
            list.push({
                date: new Date(hari).toISOString(),
                label: item.car_nama,
            });
        }
    }

    return list;
});

const tanggalDipilih = ref<Date>(new Date());

function pilihTanggal(date: Date): void {
    tanggalDipilih.value = date;
}

function aktifPadaTanggal(date: Date): MockPeminjaman[] {
    const waktu = date.getTime();

    return mockPeminjaman.filter((item) => {
        if (item.status !== 'disetujui' && item.status !== 'selesai') {
            return false;
        }

        const mulai = new Date(item.tanggal_mulai);
        const selesai = new Date(item.tanggal_selesai);
        mulai.setHours(0, 0, 0, 0);
        selesai.setHours(23, 59, 59, 999);

        return waktu >= mulai.getTime() && waktu <= selesai.getTime();
    });
}

const jadwalHariIni = computed(() => aktifPadaTanggal(tanggalDipilih.value));
</script>

<template>
    <Head title="Jadwal Mobil" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <Heading
            title="Jadwal Mobil"
            description="Kalender peminjaman mobil operasional. Klik tanggal untuk melihat detail jadwal."
        />

        <div class="grid gap-4 lg:grid-cols-5">
            <Card class="lg:col-span-3">
                <CardHeader>
                    <CardTitle>Kalender Peminjaman</CardTitle>
                    <CardDescription>
                        Penanda menunjukkan mobil yang sedang dijadwalkan pada
                        tanggal tersebut.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <MonthCalendar :events="events" @select="pilihTanggal" />
                </CardContent>
            </Card>

            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle
                        >Jadwal {{ formatShortDate(tanggalDipilih) }}</CardTitle
                    >
                    <CardDescription>
                        {{ jadwalHariIni.length }} jadwal peminjaman pada
                        tanggal ini.
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-3">
                    <div
                        v-for="item in jadwalHariIni"
                        :key="item.id"
                        class="rounded-lg border p-3"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium">
                                {{ item.car_nama }}
                            </p>
                            <StatusBadge :status="item.status" />
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ formatTime(item.tanggal_mulai) }} —
                            {{ formatTime(item.tanggal_selesai) }}
                        </p>
                        <p class="mt-1.5 text-xs">
                            <span class="font-medium">{{
                                item.nama_peminjam
                            }}</span>
                            · {{ item.divisi }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ item.keperluan }} → {{ item.lokasi_tujuan }}
                        </p>
                        <Badge
                            v-if="item.nama_customer"
                            variant="outline"
                            class="mt-2"
                        >
                            {{ item.nama_customer }}
                        </Badge>
                    </div>

                    <p
                        v-if="jadwalHariIni.length === 0"
                        class="py-10 text-center text-sm text-muted-foreground"
                    >
                        Tidak ada jadwal peminjaman pada tanggal ini.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
