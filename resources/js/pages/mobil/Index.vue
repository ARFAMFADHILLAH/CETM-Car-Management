<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Car as CarIcon, Pencil, Plus, Trash2 } from '@lucide/vue';
import { computed, reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { carStatusLabel, mockCars } from '@/mock/cars';
import type { CarStatus, MockCar } from '@/mock/cars';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Data Mobil', href: '/data-mobil' }],
    },
});

const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role?.role === 'admin');

const cars = ref<MockCar[]>([...mockCars]);

const dialogTerbuka = ref(false);
const sedangEdit = ref<MockCar | null>(null);

const form = reactive({
    nama: '',
    nomor_plat: '',
    status: 'tersedia' as CarStatus,
});

function bukaTambah(): void {
    sedangEdit.value = null;
    form.nama = '';
    form.nomor_plat = '';
    form.status = 'tersedia';
    dialogTerbuka.value = true;
}

function bukaEdit(car: MockCar): void {
    sedangEdit.value = car;
    form.nama = car.nama;
    form.nomor_plat = car.nomor_plat;
    form.status = car.status;
    dialogTerbuka.value = true;
}

function simpan(): void {
    if (sedangEdit.value) {
        const target = cars.value.find((c) => c.id === sedangEdit.value?.id);

        if (target) {
            target.nama = form.nama;
            target.nomor_plat = form.nomor_plat;
            target.status = form.status;
        }

        toast.success('Data mobil berhasil diperbarui.');
    } else {
        cars.value.push({
            id: Math.max(0, ...cars.value.map((c) => c.id)) + 1,
            nama: form.nama,
            nomor_plat: form.nomor_plat,
            status: form.status,
        });
        toast.success('Mobil baru berhasil ditambahkan.');
    }

    dialogTerbuka.value = false;
}

function hapus(car: MockCar): void {
    cars.value = cars.value.filter((c) => c.id !== car.id);
    toast.info(`Mobil ${car.nama} dihapus.`);
}
</script>

<template>
    <Head title="Data Mobil" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <Heading
                title="Data Mobil"
                description="Daftar mobil operasional beserta status ketersediaannya."
            />
            <Button v-if="isAdmin" @click="bukaTambah">
                <Plus class="mr-1 size-4" />
                Tambah Mobil
            </Button>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <Card
                v-for="car in cars"
                :key="car.id"
                class="overflow-hidden pt-0"
            >
                <div
                    class="relative aspect-video overflow-hidden border-b border-sidebar-border/70"
                >
                    <PlaceholderPattern />
                    <span
                        class="absolute top-3 left-3 flex size-10 items-center justify-center rounded-lg bg-background/90 shadow-sm"
                    >
                        <CarIcon class="size-5 text-primary" />
                    </span>
                    <div class="absolute top-3 right-3">
                        <StatusBadge :status="car.status" />
                    </div>
                </div>

                <CardHeader>
                    <CardTitle class="text-base">{{ car.nama }}</CardTitle>
                    <p class="font-mono text-sm text-muted-foreground">
                        {{ car.nomor_plat }}
                    </p>
                </CardHeader>

                <CardContent
                    v-if="isAdmin"
                    class="pb-0 text-xs text-muted-foreground"
                >
                    Status saat ini: {{ carStatusLabel[car.status] }}
                </CardContent>

                <CardFooter v-if="isAdmin" class="justify-end gap-2">
                    <Button size="sm" variant="outline" @click="bukaEdit(car)">
                        <Pencil class="size-4" />
                        Ubah
                    </Button>
                    <Button size="sm" variant="destructive" @click="hapus(car)">
                        <Trash2 class="size-4" />
                        Hapus
                    </Button>
                </CardFooter>
            </Card>
        </div>

        <!-- Dialog tambah/ubah mobil -->
        <Dialog
            :open="dialogTerbuka"
            @update:open="(open) => (dialogTerbuka = open)"
        >
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle
                        >{{ sedangEdit ? 'Ubah' : 'Tambah' }} Mobil</DialogTitle
                    >
                    <DialogDescription>
                        Lengkapi data mobil di bawah ini.
                    </DialogDescription>
                </DialogHeader>

                <form
                    id="form-mobil"
                    class="grid gap-4"
                    @submit.prevent="simpan"
                >
                    <div class="grid gap-2">
                        <Label for="car-nama">Nama / jenis mobil</Label>
                        <Input
                            id="car-nama"
                            v-model="form.nama"
                            placeholder="Contoh: Toyota Avanza"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="car-plat">Nomor polisi</Label>
                        <Input
                            id="car-plat"
                            v-model="form.nomor_plat"
                            placeholder="Contoh: B 1234 XYZ"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="car-status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger id="car-status" class="w-full">
                                <SelectValue placeholder="Pilih status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="(label, value) in carStatusLabel"
                                    :key="value"
                                    :value="value"
                                >
                                    {{ label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </form>

                <DialogFooter>
                    <Button variant="outline" @click="dialogTerbuka = false"
                        >Batal</Button
                    >
                    <Button type="submit" form="form-mobil">Simpan</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
