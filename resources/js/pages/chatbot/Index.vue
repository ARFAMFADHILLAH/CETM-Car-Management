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

async function gulirKeBawah(): Promise<void> {
    await nextTick();

    kotakPesan.value?.scrollTo({ top: kotakPesan.value.scrollHeight });
}

async function kirim(): Promise<void> {
    const teks = input.value.trim();

    if (teks === '') {
        return;
    }

    pesan.value.push({ id: idBerikutnya++, peran: 'pengguna', teks });
    input.value = '';
    void gulirKeBawah();

    sedangMengetik.value = true;

    try {
        const response = await fetch('/api/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(
                    document.cookie
                        .split('; ')
                        .find((c) => c.startsWith('XSRF-TOKEN='))
                        ?.split('=')[1] ?? '',
                ),
            },
            body: JSON.stringify({ pesan: teks }),
        });

        const data = await response.json();

        pesan.value.push({
            id: idBerikutnya++,
            peran: 'bot',
            teks: data.jawaban ?? 'Maaf, terjadi kesalahan. Silakan coba lagi.',
        });
    } catch {
        pesan.value.push({
            id: idBerikutnya++,
            peran: 'bot',
            teks: 'Maaf, terjadi kesalahan koneksi. Pastikan Anda terhubung ke internet dan coba lagi.',
        });
    } finally {
        sedangMengetik.value = false;
        void gulirKeBawah();
    }
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
                        Didukung oleh AI — biasanya membalas seketika
                    </p>
                </div>
            </CardHeader>

            <CardContent class="flex-1 overflow-y-auto py-4">
                <div ref="kotakPesan" class="flex h-full flex-col space-y-3">
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
                        :disabled="sedangMengetik"
                    />
                    <Button
                        type="submit"
                        size="icon"
                        aria-label="Kirim pesan"
                        :disabled="input.trim() === '' || sedangMengetik"
                    >
                        <SendHorizonal class="size-4" />
                    </Button>
                </form>
            </CardFooter>
        </Card>
    </div>
</template>
