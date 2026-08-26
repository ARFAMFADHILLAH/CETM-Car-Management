<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

export type CalendarEvent = {
    date: string;
    label: string;
};

const props = defineProps<{
    events?: CalendarEvent[];
}>();

const emit = defineEmits<{
    select: [date: Date];
}>();

const monthNames = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

const dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

const displayedMonth = ref(new Date().getMonth());
const displayedYear = ref(new Date().getFullYear());
const selectedDate = ref<Date | null>(null);

const today = new Date();
today.setHours(0, 0, 0, 0);

const calendarDays = computed<(Date | null)[]>(() => {
    const firstDay = new Date(displayedYear.value, displayedMonth.value, 1);
    // Monday-first offset (0 = Senin)
    const startOffset = (firstDay.getDay() + 6) % 7;

    const daysInMonth = new Date(
        displayedYear.value,
        displayedMonth.value + 1,
        0,
    ).getDate();

    const days: (Date | null)[] = Array.from(
        { length: startOffset },
        () => null,
    );

    for (let day = 1; day <= daysInMonth; day++) {
        days.push(new Date(displayedYear.value, displayedMonth.value, day));
    }

    return days;
});

function eventDates(): Map<string, CalendarEvent[]> {
    const map = new Map<string, CalendarEvent[]>();

    for (const event of props.events ?? []) {
        const key = new Date(event.date).toDateString();
        const list = map.get(key) ?? [];
        list.push(event);
        map.set(key, list);
    }

    return map;
}

const eventsByDate = eventDates();

function eventsFor(date: Date): CalendarEvent[] {
    return eventsByDate.get(date.toDateString()) ?? [];
}

function isToday(date: Date): boolean {
    return date.getTime() === today.getTime();
}

function isSelected(date: Date): boolean {
    return (
        selectedDate.value !== null &&
        date.getTime() === selectedDate.value.getTime()
    );
}

function previousMonth(): void {
    if (displayedMonth.value === 0) {
        displayedMonth.value = 11;
        displayedYear.value--;
    } else {
        displayedMonth.value--;
    }
}

function nextMonth(): void {
    if (displayedMonth.value === 11) {
        displayedMonth.value = 0;
        displayedYear.value++;
    } else {
        displayedMonth.value++;
    }
}

function backToToday(): void {
    displayedMonth.value = today.getMonth();
    displayedYear.value = today.getFullYear();
}

function select(date: Date): void {
    selectedDate.value = date;
    emit('select', date);
}
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <Button
                variant="outline"
                size="icon"
                aria-label="Bulan sebelumnya"
                @click="previousMonth"
            >
                <ChevronLeft class="size-4" />
            </Button>

            <p class="text-sm font-semibold">
                {{ monthNames[displayedMonth] }} {{ displayedYear }}
            </p>

            <div class="flex items-center gap-1">
                <Button
                    variant="ghost"
                    size="sm"
                    class="text-xs"
                    @click="backToToday"
                >
                    Hari ini
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    aria-label="Bulan berikutnya"
                    @click="nextMonth"
                >
                    <ChevronRight class="size-4" />
                </Button>
            </div>
        </div>

        <div
            class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-muted-foreground"
        >
            <span v-for="name in dayNames" :key="name">{{ name }}</span>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <template v-for="(date, index) in calendarDays" :key="index">
                <button
                    v-if="date"
                    type="button"
                    :class="
                        cn(
                            'flex h-16 flex-col items-center justify-start gap-1 rounded-lg border p-1.5 text-sm transition-colors hover:bg-accent',
                            isSelected(date)
                                ? 'border-primary bg-primary/10'
                                : 'border-transparent bg-muted/40',
                        )
                    "
                    @click="select(date)"
                >
                    <span
                        :class="
                            cn(
                                'flex size-6 items-center justify-center rounded-full',
                                isToday(date) &&
                                    'bg-primary font-semibold text-primary-foreground',
                            )
                        "
                    >
                        {{ date.getDate() }}
                    </span>
                    <span
                        class="flex w-full flex-wrap justify-center gap-0.5 overflow-hidden"
                    >
                        <template v-if="eventsFor(date).length > 2">
                            <span
                                class="text-[10px] leading-tight text-muted-foreground"
                            >
                                {{ eventsFor(date).length }} jadwal
                            </span>
                        </template>
                        <template v-else>
                            <span
                                v-for="event in eventsFor(date)"
                                :key="event.label"
                                class="w-full truncate rounded-sm bg-primary/15 px-1 text-[10px] leading-tight text-primary"
                                :title="event.label"
                            >
                                {{ event.label }}
                            </span>
                        </template>
                    </span>
                </button>
                <span v-else class="h-16 rounded-lg bg-transparent" />
            </template>
        </div>
    </div>
</template>
