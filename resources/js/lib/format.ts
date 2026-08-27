const dateFormatter = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const shortDateFormatter = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
});

const dateTimeFormatter = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

const timeFormatter = new Intl.DateTimeFormat('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
});

export function formatDate(value: string | Date): string {
    return dateFormatter.format(new Date(value));
}

export function formatShortDate(value: string | Date): string {
    return shortDateFormatter.format(new Date(value));
}

export function formatDateTime(value: string | Date): string {
    return dateTimeFormatter.format(new Date(value));
}

export function formatTime(value: string | Date): string {
    return timeFormatter.format(new Date(value));
}

export function waktuRelatif(value: string | Date): string {
    const selisih = Date.now() - new Date(value).getTime();
    const menit = Math.floor(selisih / (1000 * 60));

    if (menit < 1) {
        return 'Baru saja';
    }

    if (menit < 60) {
        return `${menit} menit lalu`;
    }

    const jam = Math.floor(menit / 60);

    if (jam < 24) {
        return `${jam} jam lalu`;
    }

    return `${Math.floor(jam / 24)} hari lalu`;
}
