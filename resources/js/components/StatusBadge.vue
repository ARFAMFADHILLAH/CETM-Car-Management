<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { carStatusLabel } from '@/mock/cars';
import type { CarStatus } from '@/mock/cars';
import { peminjamanStatusLabel } from '@/mock/peminjaman';
import type { PeminjamanStatus } from '@/mock/peminjaman';

export type AppStatus = CarStatus | PeminjamanStatus;

const props = defineProps<{
    status: AppStatus;
}>();

const config = computed<
    { label: string; class: string } | { label: undefined; class: undefined }
>(() => {
    const labels: Record<string, string> = {
        ...carStatusLabel,
        ...peminjamanStatusLabel,
    };

    const classes: Record<AppStatus, string> = {
        tersedia:
            'bg-green-500 text-white border-green-500',
        tidak_tersedia:
            'bg-red-500 text-white border-red-500',
        di_servis:
            'bg-yellow-500 text-white border-yellow-500',
        pending:
            'bg-yellow-500 text-white border-yellow-500',
        disetujui:
            'bg-green-500 text-white border-green-500',
        ditolak:
            'bg-red-500 text-white border-red-500',
        selesai:
            'bg-green-600 text-white border-green-600',
    };

    const label = labels[props.status];

    if (label === undefined) {
        return { label: undefined, class: undefined };
    }

    return { label, class: classes[props.status] };
});
</script>

<template>
    <Badge v-if="config.label" variant="outline" :class="config.class">
        {{ config.label }}
    </Badge>
</template>
