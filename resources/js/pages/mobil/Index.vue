<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Car as CarIcon, Image, Pencil, Plus, Trash2, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
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
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Car as CarType, CarStatus } from '@/types/car';
import { store, update, destroy } from '@/routes/mobil';

const carStatusLabel: Record<CarStatus, string> = {
    tersedia: 'Tersedia',
    tidak_tersedia: 'Tidak Tersedia',
    di_servis: 'Di Servis',
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Data Mobil', href: '/data-mobil' }],
    },
});

const page = usePage();
const isAdmin = computed(() => page.props.auth.user?.role?.role === 'admin');

const props = defineProps<{
    cars: CarType[];
}>();

const dialogTerbuka = ref(false);
const sedangEdit = ref<CarType | null>(null);
const previewUrl = ref<string | null>(null);

const form = useForm({
    nama: '',
    nomor_plat: '',
    status: 'tersedia' as CarStatus,
    foto: null as File | null,
});

watch(
    () => page.props.flash?.success,
    (msg) => {
        if (msg) {
            import('vue-sonner').then(({ toast }) => toast.success(msg as string));
        }
    },
);

function bukaTambah(): void {
    sedangEdit.value = null;
    form.reset();
    form.clearErrors();
    previewUrl.value = null;
    dialogTerbuka.value = true;
}

function bukaEdit(car: CarType): void {
    sedangEdit.value = car;
    form.nama = car.nama;
    form.nomor_plat = car.nomor_plat;
    form.status = car.status;
    form.foto = null;
    previewUrl.value = car.foto ? `/storage/${car.foto}` : null;
    form.clearErrors();
    dialogTerbuka.value = true;
}

function handleFoto(e: Event): void {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;
    form.foto = file;

    if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = file ? URL.createObjectURL(file) : null;
}

function hapusPreview(): void {
    if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = null;
    form.foto = null;
}

function simpan(): void {
    if (sedangEdit.value) {
        form.put(update.url(sedangEdit.value.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                dialogTerbuka.value = false;
            },
        });
    } else {
        form.post(store.url(), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                dialogTerbuka.value = false;
            },
        });
    }
}

function hapus(car: CarType): void {
    if (!confirm(`Hapus mobil "${car.nama}"?`)) {
        return;
    }

    router.delete(destroy.url(car.id), {
        preserveScroll: true,
    });
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
                    <img
                        v-if="car.foto"
                        :src="`/storage/${car.foto}`"
                        :alt="car.nama"
                        class="size-full object-cover"
                    />
                    <template v-else>
                        <PlaceholderPattern />
                        <span
                            class="absolute top-3 left-3 flex size-10 items-center justify-center rounded-lg bg-background/90 shadow-sm"
                        >
                            <CarIcon class="size-5 text-primary" />
                        </span>
                    </template>
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
                    <Button
                        size="sm"
                        variant="destructive"
                        @click="hapus(car)"
                    >
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
                        />
                        <InputError :message="form.errors.nama" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="car-plat">Nomor polisi</Label>
                        <Input
                            id="car-plat"
                            v-model="form.nomor_plat"
                            placeholder="Contoh: B 1234 XYZ"
                        />
                        <InputError :message="form.errors.nomor_plat" />
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
                        <InputError :message="form.errors.status" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="car-foto">Foto mobil (opsional)</Label>
                        <div v-if="previewUrl" class="relative">
                            <img
                                :src="previewUrl"
                                alt="Preview"
                                class="aspect-video w-full rounded-md border object-cover"
                            />
                            <Button
                                type="button"
                                variant="destructive"
                                size="icon"
                                class="absolute top-2 right-2 size-7"
                                @click="hapusPreview"
                            >
                                <X class="size-4" />
                            </Button>
                        </div>
                        <div v-else>
                            <Label
                                for="car-foto"
                                class="flex cursor-pointer flex-col items-center gap-2 rounded-md border border-dashed p-6 text-muted-foreground transition-colors hover:border-primary/50 hover:text-primary"
                            >
                                <Image class="size-8" />
                                <span class="text-sm">Pilih foto dari komputer</span>
                                <span class="text-xs"
                                    >JPG, PNG, atau WebP (maks. 2 MB)</span
                                >
                            </Label>
                        </div>
                        <Input
                            id="car-foto"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                            @change="handleFoto"
                        />
                        <InputError :message="form.errors.foto" />
                    </div>
                </form>

                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="dialogTerbuka = false"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        form="form-mobil"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
