<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Bot, SendHorizonal } from '@lucide/vue';
import { nextTick, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Chatbot', href: '/chatbot' }],
    },
});

type Pesan = {
    id: number;
    peran: 'pengguna' | 'bot';
    teks: string;
};

let idBerikutnya = 1;

const pesan = ref<Pesan[]>([
    {
        id: idBerikutnya++,
        peran: 'bot',
        teks: 'Halo! Saya asisten virtual CETM. Tanyakan seputar peminjaman mobil, jadwal, atau status pengajuan Anda.',
    },
]);

const input = ref('');
const sedangMengetik = ref(false);
const kotakPesan = ref<HTMLElement | null>(null);

const balasanBot = [
    'Untuk mengajukan peminjaman mobil, buka menu "Ajukan Peminjaman" di sidebar lalu lengkapi formulirnya. Pengajuan akan diproses oleh admin.',
    'Status pengajuan dapat Anda pantau di menu "Daftar Pengajuan". Status meliputi menunggu, disetujui, ditolak, dan selesai.',
    'Jadwal seluruh mobil bisa dilihat di menu "Jadwal Mobil" berbentuk kalender bulanan. Klik tanggal untuk melihat detailnya.',
    'Mobil dengan status "Tersedia" di halaman "Data Mobil" siap dipinjam. Mobil yang "Di Servis" tidak dapat diajukan sementara.',
    'Jika ada kendala, silakan hubungi admin melalui menu notifikasi atau datang langsung ke kantor bagian umum.',
];

function jawabanBot(tanyaan: string): string {
    const teks = tanyaan.toLowerCase();

    if (teks.includes('pinjam') || teks.includes('ajukan')) {
        return balasanBot[0];
    }

    if (teks.includes('status') || teks.includes('pengajuan')) {
        return balasanBot[1];
    }

    if (teks.includes('jadwal') || teks.includes('kalender')) {
        return balasanBot[2];
    }

    if (teks.includes('mobil') || teks.includes('servis')) {
        return balasanBot[3];
    }

    return balasanBot[Math.floor(Math.random() * balasanBot.length)];
}

async function gulirKeBawah(): Promise<void> {
    await nextTick();

    kotakPesan.value?.scrollTo({ top: kotakPesan.value.scrollHeight });
}

function kirim(): void {
    const teks = input.value.trim();

    if (teks === '') {
        return;
    }

    pesan.value.push({ id: idBerikutnya++, peran: 'pengguna', teks });
    input.value = '';
    void gulirKeBawah();

    sedangMengetik.value = true;

    setTimeout(() => {
        pesan.value.push({
            id: idBerikutnya++,
            peran: 'bot',
            teks: jawabanBot(teks),
        });
        sedangMengetik.value = false;
        void gulirKeBawah();
    }, 800);
}
</script>

<template>
    <Head title="Chatbot" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <Heading
            title="Chatbot"
            description="Asisten virtual untuk membantu pertanyaan seputar peminjaman mobil."
        />

        <Card class="mx-auto flex h-[32rem] w-full max-w-2xl flex-col">
            <CardHeader class="flex-row items-center gap-3 border-b">
                <span
                    class="flex size-9 items-center justify-center rounded-full bg-primary/10 text-primary"
                >
                    <Bot class="size-5" />
                </span>
                <div>
                    <p class="text-sm font-semibold">Asisten CETM</p>
                    <p class="text-xs text-muted-foreground">
                        Biasanya membalas seketika
                    </p>
                </div>
            </CardHeader>

            <CardContent
                ref="kotakPesan"
                class="flex-1 space-y-3 overflow-y-auto py-4"
            >
                <div
                    v-for="item in pesan"
                    :key="item.id"
                    class="flex"
                    :class="
                        item.peran === 'pengguna'
                            ? 'justify-end'
                            : 'justify-start'
                    "
                >
                    <div
                        class="max-w-[80%] rounded-2xl px-3.5 py-2.5 text-sm"
                        :class="
                            item.peran === 'pengguna'
                                ? 'rounded-br-sm bg-primary text-primary-foreground'
                                : 'rounded-bl-sm bg-muted'
                        "
                    >
                        {{ item.teks }}
                    </div>
                </div>

                <div v-if="sedangMengetik" class="flex justify-start">
                    <div
                        class="rounded-2xl rounded-bl-sm bg-muted px-3.5 py-2.5"
                    >
                        <span class="flex gap-1">
                            <span
                                class="size-1.5 animate-bounce rounded-full bg-muted-foreground/60 [animation-delay:-0.3s]"
                            />
                            <span
                                class="size-1.5 animate-bounce rounded-full bg-muted-foreground/60 [animation-delay:-0.15s]"
                            />
                            <span
                                class="size-1.5 animate-bounce rounded-full bg-muted-foreground/60"
                            />
                        </span>
                    </div>
                </div>
            </CardContent>

            <CardFooter class="border-t pt-4">
                <form
                    class="flex w-full items-center gap-2"
                    @submit.prevent="kirim"
                >
                    <Input
                        v-model="input"
                        placeholder="Tulis pertanyaan Anda..."
                        aria-label="Pesan"
                    />
                    <Button
                        type="submit"
                        size="icon"
                        aria-label="Kirim pesan"
                        :disabled="input.trim() === ''"
                    >
                        <SendHorizonal class="size-4" />
                    </Button>
                </form>
            </CardFooter>
        </Card>
    </div>
</template>
