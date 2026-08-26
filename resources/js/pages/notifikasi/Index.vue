<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BellRing, CheckCheck, CheckCircle2, Info, XCircle } from '@lucide/vue';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { mockNotifikasi } from '@/mock/notifikasi';
import type { MockNotifikasi } from '@/mock/notifikasi';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Notifikasi', href: '/notifikasi' }],
    },
});

const notifikasi = ref<MockNotifikasi[]>([...mockNotifikasi]);
const tabAktif = ref<'semua' | 'belum'>('semua');

const daftar = computed(() =>
    tabAktif.value === 'belum'
        ? notifikasi.value.filter((item) => !item.dibaca)
        : notifikasi.value,
);

const belumDibaca = computed(
    () => notifikasi.value.filter((item) => !item.dibaca).length,
);

function tandaiDibaca(item: MockNotifikasi): void {
    item.dibaca = true;
}

function tandaiSemua(): void {
    for (const item of notifikasi.value) {
        item.dibaca = true;
    }
}

function ikonTipe(tipe: MockNotifikasi['tipe']): typeof Info {
    const map: Record<MockNotifikasi['tipe'], typeof Info> = {
        disetujui: CheckCircle2,
        ditolak: XCircle,
        pengingat: BellRing,
        info: Info,
    };

    return map[tipe];
}

function waktuRelatif(iso: string): string {
    const selisih = Date.now() - new Date(iso).getTime();
    const jam = Math.floor(selisih / (1000 * 60 * 60));

    if (jam < 1) {
        return 'Baru saja';
    }

    if (jam < 24) {
        return `${jam} jam lalu`;
    }

    return `${Math.floor(jam / 24)} hari lalu`;
}
</script>

<template>
    <Head title="Notifikasi" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Heading
                title="Notifikasi"
                description="Pemberitahuan seputar pengajuan peminjaman dan jadwal mobil."
            />
            <Button
                variant="outline"
                :disabled="belumDibaca === 0"
                @click="tandaiSemua"
            >
                <CheckCheck class="mr-1 size-4" />
                Tandai semua dibaca
            </Button>
        </div>

        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle>Kotak Masuk</CardTitle>
                <div class="flex items-center gap-1 rounded-lg bg-muted p-1">
                    <Button
                        size="sm"
                        variant="ghost"
                        :class="
                            tabAktif === 'semua'
                                ? 'bg-background shadow-sm'
                                : ''
                        "
                        @click="tabAktif = 'semua'"
                    >
                        Semua
                    </Button>
                    <Button
                        size="sm"
                        variant="ghost"
                        :class="
                            tabAktif === 'belum'
                                ? 'bg-background shadow-sm'
                                : ''
                        "
                        @click="tabAktif = 'belum'"
                    >
                        Belum dibaca
                        <Badge v-if="belumDibaca > 0" class="ml-1.5">{{
                            belumDibaca
                        }}</Badge>
                    </Button>
                </div>
            </CardHeader>
            <CardContent class="flex flex-col divide-y">
                <div
                    v-for="item in daftar"
                    :key="item.id"
                    class="flex items-start gap-3 py-4 first:pt-0 last:pb-0"
                    :class="{ 'rounded-lg bg-muted/40 px-3': !item.dibaca }"
                >
                    <span
                        class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg"
                        :class="{
                            'bg-green-500 text-white':
                                item.tipe === 'disetujui',
                            'bg-red-500 text-white':
                                item.tipe === 'ditolak',
                            'bg-yellow-500 text-white':
                                item.tipe === 'pengingat',
                            'bg-blue-500 text-white':
                                item.tipe === 'info',
                        }"
                    >
                        <component :is="ikonTipe(item.tipe)" class="size-4" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium">
                            {{ item.judul }}
                            <span
                                v-if="!item.dibaca"
                                class="ml-2 inline-block size-2 rounded-full bg-primary align-middle"
                                title="Belum dibaca"
                            />
                        </p>
                        <p class="mt-0.5 text-sm text-muted-foreground">
                            {{ item.pesan }}
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground/70">
                            {{ waktuRelatif(item.waktu) }}
                        </p>
                    </div>

                    <Button
                        v-if="!item.dibaca"
                        size="sm"
                        variant="ghost"
                        @click="tandaiDibaca(item)"
                    >
                        Tandai dibaca
                    </Button>
                </div>

                <p
                    v-if="daftar.length === 0"
                    class="py-10 text-center text-sm text-muted-foreground"
                >
                    Tidak ada notifikasi.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
