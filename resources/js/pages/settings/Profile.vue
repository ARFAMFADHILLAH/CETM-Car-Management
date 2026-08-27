<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengaturan Profil',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const previewUrl = ref<string | null>((user.value.foto_url as string) ?? null);
const inputFoto = ref<HTMLInputElement | null>(null);

const form = useForm({
    nama: user.value.nama,
    no_hp: user.value.no_hp ?? '',
    email: user.value.email,
    foto: null as File | null,
});

function inisial(nama: string): string {
    return nama
        .split(' ')
        .map((w) => w.charAt(0))
        .slice(0, 2)
        .join('')
        .toUpperCase();
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

function hapusFoto(): void {
    if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = null;
    form.foto = null;
}

function bukaFilePicker(): void {
    inputFoto.value?.click();
}

function simpan(): void {
    form.patch(ProfileController.update.url(), {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Pengaturan Profil" />

    <h1 class="sr-only">Pengaturan Profil</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profil"
            description="Perbarui nama, email, dan foto profil Anda"
        />

        <form class="space-y-6" @submit.prevent="simpan">
            <div class="flex items-center gap-6">
                <Avatar class="size-20">
                    <AvatarImage
                        v-if="previewUrl"
                        :src="previewUrl"
                        :alt="user.nama"
                    />
                    <AvatarFallback class="text-lg">
                        {{ inisial(user.nama) }}
                    </AvatarFallback>
                </Avatar>

                <div class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="bukaFilePicker"
                        >
                            Ganti Foto
                        </Button>
                        <Button
                            v-if="previewUrl"
                            type="button"
                            variant="destructive"
                            size="sm"
                            @click="hapusFoto"
                        >
                            Hapus
                        </Button>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        JPG, PNG, atau WebP (maks. 2 MB)
                    </p>
                    <input
                        ref="inputFoto"
                        id="profile-foto"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                        @change="handleFoto"
                    />
                    <InputError :message="form.errors.foto" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="nama">Nama</Label>
                <Input
                    id="nama"
                    v-model="form.nama"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                    placeholder="Nama lengkap"
                />
                <InputError class="mt-2" :message="form.errors.nama" />
            </div>

            <div class="grid gap-2">
                <Label for="no_hp">Nomor HP</Label>
                <Input
                    id="no_hp"
                    v-model="form.no_hp"
                    type="tel"
                    class="mt-1 block w-full"
                    autocomplete="tel"
                    placeholder="08xxxxxxxxxx"
                />
                <InputError class="mt-2" :message="form.errors.no_hp" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Alamat Email</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                    placeholder="Alamat email"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="page.props.mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Alamat email Anda belum diverifikasi.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-if="page.props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-blue-600"
                >
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing" data-test="update-profile-button"
                    >Simpan</Button
                >
            </div>
        </form>
    </div>

    <DeleteUser />
</template>
