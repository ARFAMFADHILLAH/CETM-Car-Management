<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { store, availableCars } from '@/routes/peminjaman';
import type { Divisi } from '@/types/divisi';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Ajukan Peminjaman', href: '/ajukan-peminjaman' },
        ],
    },
});

const props = defineProps<{
    divisiList: Divisi[];
    mobilTersedia: { id: number; nama: string; nomor_plat: string }[];
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

function defaultMulai(): Date {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    d.setHours(8, 0, 0, 0);
    return d;
}

function defaultSelesai(): Date {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    d.setHours(17, 0, 0, 0);
    return d;
}

const tanggalMulai = ref(defaultMulai());
const tanggalSelesai = ref(defaultSelesai());

const mobilList = ref(props.mobilTersedia);
const loadingMobil = ref(false);

const form = useForm({
    nama_peminjam: user.value?.nama ?? '',
    email_peminjam: user.value?.email ?? '',
    no_hp: user.value?.no_hp ?? '',
    divisi_id: '' as string | number,
    car_id: '' as string | number,
    tanggal_mulai: '',
    tanggal_selesai: '',
    tujuan: '',
    keperluan: '',
    km_awal: '' as string | number,
    km_akhir: '' as string | number,
    tangki_bbm: '',
    lokasi_tujuan: '',
    nama_customer: '',
    catatan: '',
});

let fetchTimeout: ReturnType<typeof setTimeout> | null = null;

function fetchAvailableCars(): void {
    if (fetchTimeout) {
        clearTimeout(fetchTimeout);
    }

    if (!tanggalMulai.value || !tanggalSelesai.value) {
        return;
    }

    fetchTimeout = setTimeout(async () => {
        loadingMobil.value = true;
        form.car_id = '';

        try {
            const url = availableCars.url({
                query: {
                    tanggal_mulai: tanggalMulai.value?.toISOString() ?? '',
                    tanggal_selesai: tanggalSelesai.value?.toISOString() ?? '',
                },
            });

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
            });

            if (response.ok) {
                mobilList.value = await response.json();
            }
        } catch {
            mobilList.value = props.mobilTersedia;
        } finally {
            loadingMobil.value = false;
        }
    }, 400);
}

watch([tanggalMulai, tanggalSelesai], fetchAvailableCars);

function kirim(): void {
    form.tanggal_mulai = tanggalMulai.value?.toISOString() ?? '';
    form.tanggal_selesai = tanggalSelesai.value?.toISOString() ?? '';

    form.post(store.url(), {
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="Ajukan Peminjaman" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <Heading
            title="Ajukan Peminjaman"
            description="Lengkapi formulir berikut untuk mengajukan peminjaman mobil operasional."
        />

        <Card class="mx-auto w-full max-w-3xl">
            <CardHeader>
                <CardTitle>Formulir Peminjaman</CardTitle>
                <CardDescription>
                    Pengajuan akan diperiksa oleh admin sebelum disetujui.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form class="grid gap-5 md:grid-cols-2" @submit.prevent="kirim">
                    <div class="grid gap-2">
                        <Label for="nama_peminjam">Nama peminjam</Label>
                        <Input id="nama_peminjam" v-model="form.nama_peminjam" required />
                        <InputError :message="form.errors.nama_peminjam" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email_peminjam">Email</Label>
                        <Input
                            id="email_peminjam"
                            v-model="form.email_peminjam"
                            type="email"
                            required
                        />
                        <InputError :message="form.errors.email_peminjam" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="no_hp">Nomor HP</Label>
                        <Input
                            id="no_hp"
                            v-model="form.no_hp"
                            type="tel"
                            placeholder="08xxxxxxxxxx"
                        />
                        <InputError :message="form.errors.no_hp" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="divisi_id">Divisi</Label>
                        <Select v-model="form.divisi_id" required>
                            <SelectTrigger id="divisi_id" class="w-full">
                                <SelectValue placeholder="Pilih divisi" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="divisi in divisiList"
                                    :key="divisi.id"
                                    :value="divisi.id"
                                >
                                    {{ divisi.nama_divisi }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.divisi_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Pinjam dari</Label>
                        <VueDatePicker
                            v-model="tanggalMulai"
                            format="dd/MM/yy HH:mm"
                            :format-locale="{
                                code: 'id-ID',
                                localize: { month: 'short' },
                            }"
                            enable-seconds
                            text-input
                            auto-apply
                            teleport
                            :teleport-center="false"
                            placeholder="Pilih tanggal & jam"
                            :ui="{
                                input: 'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
                            }"
                            :dark="false"
                        />
                        <InputError :message="form.errors.tanggal_mulai" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Pinjam sampai</Label>
                        <VueDatePicker
                            v-model="tanggalSelesai"
                            format="dd/MM/yy HH:mm"
                            :format-locale="{
                                code: 'id-ID',
                                localize: { month: 'short' },
                            }"
                            enable-seconds
                            text-input
                            auto-apply
                            teleport
                            :teleport-center="false"
                            placeholder="Pilih tanggal & jam"
                            :ui="{
                                input: 'flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
                            }"
                            :dark="false"
                        />
                        <InputError :message="form.errors.tanggal_selesai" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="car_id">Mobil yang dipinjam</Label>
                        <Select v-model="form.car_id" required :disabled="loadingMobil">
                            <SelectTrigger id="car_id" class="w-full">
                                <SelectValue>
                                    <span v-if="loadingMobil">Mencari mobil tersedia...</span>
                                    <span v-else-if="form.car_id && mobilList.length > 0">
                                        {{ mobilList.find(c => c.id === form.car_id)?.nama }} — {{ mobilList.find(c => c.id === form.car_id)?.nomor_plat }}
                                    </span>
                                    <span v-else>Pilih mobil</span>
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="car in mobilList"
                                    :key="car.id"
                                    :value="car.id"
                                >
                                    {{ car.nama }} — {{ car.nomor_plat }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="loadingMobil" class="text-xs text-muted-foreground">
                            Memeriksa ketersediaan mobil...
                        </p>
                        <p v-else-if="mobilList.length === 0" class="text-xs text-destructive">
                            Tidak ada mobil tersedia untuk rentang tanggal ini. Silakan ubah tanggal atau hubungi admin.
                        </p>
                        <p v-else class="text-xs text-muted-foreground">
                            Hanya mobil tersedia untuk tanggal yang dipilih yang ditampilkan.
                        </p>
                        <InputError :message="form.errors.car_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Tujuan</Label>
                        <div class="flex gap-4 pt-1">
                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    v-model="form.tujuan"
                                    type="radio"
                                    value="dalam_kota"
                                    class="h-4 w-4 border-primary text-primary accent-primary"
                                    required
                                />
                                Dalam Kota
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input
                                    v-model="form.tujuan"
                                    type="radio"
                                    value="luar_kota"
                                    class="h-4 w-4 border-primary text-primary accent-primary"
                                />
                                Luar Kota
                            </label>
                        </div>
                        <InputError :message="form.errors.tujuan" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="keperluan">Keperluan</Label>
                        <Input
                            id="keperluan"
                            v-model="form.keperluan"
                            placeholder="Contoh: kunjungan client"
                            required
                        />
                        <InputError :message="form.errors.keperluan" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="km_awal">KM Awal</Label>
                        <Input
                            id="km_awal"
                            v-model="form.km_awal"
                            type="number"
                            min="0"
                            placeholder="Contoh: 12500"
                            required
                        />
                        <InputError :message="form.errors.km_awal" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="km_akhir">KM Akhir</Label>
                        <Input
                            id="km_akhir"
                            v-model="form.km_akhir"
                            type="number"
                            min="0"
                            placeholder="Contoh: 12650"
                        />
                        <InputError :message="form.errors.km_akhir" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="tangki_bbm">Tangki BBM</Label>
                        <Select v-model="form.tangki_bbm" required>
                            <SelectTrigger id="tangki_bbm" class="w-full">
                                <SelectValue placeholder="Pilih level BBM" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="full">Penuh</SelectItem>
                                <SelectItem value="3/4">3/4</SelectItem>
                                <SelectItem value="1/2">1/2</SelectItem>
                                <SelectItem value="1/4">1/4</SelectItem>
                                <SelectItem value="empty">Kosong</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.tangki_bbm" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="lokasi_tujuan">Lokasi tujuan</Label>
                        <Input
                            id="lokasi_tujuan"
                            v-model="form.lokasi_tujuan"
                            placeholder="Contoh: Bandung"
                            required
                        />
                        <InputError :message="form.errors.lokasi_tujuan" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="nama_customer">Nama customer (opsional)</Label>
                        <Input
                            id="nama_customer"
                            v-model="form.nama_customer"
                            placeholder="Contoh: PT Sinar Jaya"
                        />
                    </div>

                    <div class="grid content-end gap-2">
                        <Button
                            type="submit"
                            class="w-fit"
                            :disabled="form.processing || loadingMobil"
                        >
                            {{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan' }}
                        </Button>
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="catatan">Catatan tambahan (opsional)</Label>
                        <Textarea
                            id="catatan"
                            v-model="form.catatan"
                            placeholder="Tulis kebutuhan khusus, misal alat atau perlengkapan tambahan."
                        />
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
