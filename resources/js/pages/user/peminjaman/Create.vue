<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
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
import { mockCars } from '@/mock/cars';
import { mockDivisi } from '@/mock/pengguna';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Ajukan Peminjaman', href: '/ajukan-peminjaman' },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const mobilTersedia = computed(() =>
    mockCars.filter((car) => car.status === 'tersedia'),
);

function toLocalInput(date: Date): string {
    const pad = (n: number): string => String(n).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
        date.getDate(),
    )}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

const mulaiDefault = new Date();
mulaiDefault.setDate(mulaiDefault.getDate() + 1);
mulaiDefault.setHours(8, 0, 0, 0);

const selesaiDefault = new Date(mulaiDefault);
selesaiDefault.setHours(17, 0, 0, 0);

const form = reactive({
    nama: user.value?.nama ?? '',
    email: user.value?.email ?? '',
    no_hp: user.value?.no_hp ?? '',
    divisi: '',
    car_id: '',
    tanggal_mulai: toLocalInput(mulaiDefault),
    tanggal_selesai: toLocalInput(selesaiDefault),
    kegiatan: '',
    lokasi_tujuan: '',
    nama_customer: '',
    catatan: '',
});

function kirim(): void {
    toast.success('Pengajuan peminjaman berhasil dikirim.');
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
                        <Label for="nama">Nama peminjam</Label>
                        <Input id="nama" v-model="form.nama" required />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="no_hp">Nomor HP</Label>
                        <Input
                            id="no_hp"
                            v-model="form.no_hp"
                            type="tel"
                            placeholder="08xxxxxxxxxx"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="divisi">Divisi</Label>
                        <Select v-model="form.divisi" required>
                            <SelectTrigger id="divisi" class="w-full">
                                <SelectValue placeholder="Pilih divisi" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="divisi in mockDivisi"
                                    :key="divisi"
                                    :value="divisi"
                                >
                                    {{ divisi }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="mobil">Mobil yang dipinjam</Label>
                        <Select v-model="form.car_id" required>
                            <SelectTrigger id="mobil" class="w-full">
                                <SelectValue placeholder="Pilih mobil" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="car in mobilTersedia"
                                    :key="car.id"
                                    :value="String(car.id)"
                                >
                                    {{ car.nama }} — {{ car.nomor_plat }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-xs text-muted-foreground">
                            Hanya mobil dengan status tersedia yang dapat
                            dipilih.
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="tanggal_mulai">Pinjam dari</Label>
                        <Input
                            id="tanggal_mulai"
                            v-model="form.tanggal_mulai"
                            type="datetime-local"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="tanggal_selesai">Pinjam sampai</Label>
                        <Input
                            id="tanggal_selesai"
                            v-model="form.tanggal_selesai"
                            type="datetime-local"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="kegiatan">Kegiatan</Label>
                        <Input
                            id="kegiatan"
                            v-model="form.kegiatan"
                            placeholder="Contoh: kunjungan customer"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="lokasi_tujuan">Lokasi tujuan</Label>
                        <Input
                            id="lokasi_tujuan"
                            v-model="form.lokasi_tujuan"
                            placeholder="Contoh: Bandung"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="nama_customer"
                            >Nama customer (opsional)</Label
                        >
                        <Input
                            id="nama_customer"
                            v-model="form.nama_customer"
                            placeholder="Contoh: PT Sinar Jaya"
                        />
                    </div>

                    <div class="grid content-end gap-2">
                        <Button type="submit" class="w-fit"
                            >Kirim Pengajuan</Button
                        >
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
