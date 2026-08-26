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
