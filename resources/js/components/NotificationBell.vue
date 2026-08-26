<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Bell } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { mockNotifikasi } from '@/mock/notifikasi';
import { index as notifikasiRoute } from '@/routes/notifikasi';

const belumDibaca = computed(
    () => mockNotifikasi.filter((item) => !item.dibaca).length,
);

const terbaru = computed(() => mockNotifikasi.slice(0, 4));

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
    <DropdownMenu>
        <DropdownMenuTrigger :as-child="true">
            <Button
                variant="ghost"
                size="icon"
                class="relative"
                aria-label="Notifikasi"
            >
                <Bell class="size-5" />
                <span
                    v-if="belumDibaca > 0"
                    class="absolute -top-0.5 -right-0.5 flex size-4 items-center justify-center rounded-full bg-blue-600 text-[10px] font-semibold text-white"
                >
                    {{ belumDibaca }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-80">
            <DropdownMenuLabel>Notifikasi</DropdownMenuLabel>
            <DropdownMenuSeparator />

            <div
                v-if="terbaru.length === 0"
                class="px-3 py-6 text-center text-sm text-muted-foreground"
            >
                Tidak ada notifikasi.
            </div>

            <Link
                v-for="item in terbaru"
                :key="item.id"
                :href="notifikasiRoute()"
                class="flex flex-col gap-0.5 px-3 py-2.5 text-sm hover:bg-accent"
                :class="{ 'bg-muted/50': !item.dibaca }"
            >
                <span class="font-medium">{{ item.judul }}</span>
                <span class="line-clamp-2 text-xs text-muted-foreground">{{
                    item.pesan
                }}</span>
                <span class="text-[11px] text-muted-foreground/70">{{
                    waktuRelatif(item.waktu)
                }}</span>
            </Link>

            <DropdownMenuSeparator />
            <Link
                :href="notifikasiRoute()"
                class="block px-3 py-2 text-center text-sm font-medium text-primary hover:underline"
            >
                Lihat semua
            </Link>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
