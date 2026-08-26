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
            'border-green-200 bg-green-50 text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-400',
        tidak_tersedia:
            'border-neutral-200 bg-neutral-100 text-neutral-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400',
        di_servis:
            'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-400',
        pending:
            'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-900 dark:bg-yellow-950 dark:text-yellow-400',
        disetujui:
            'border-green-200 bg-green-50 text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-400',
        ditolak:
            'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-400',
        selesai:
            'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-400',
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
