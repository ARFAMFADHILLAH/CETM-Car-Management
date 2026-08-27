<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { KeyRound, Pencil, Plus, ShieldCheck, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { User } from '@/types/auth';
import {
    store,
    update,
    destroy,
    resetPassword,
} from '@/routes/manajemen/admin';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Manajemen Admin', href: '/manajemen-admin' }],
    },
});

const props = defineProps<{
    admins: User[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const dialogTerbuka = ref(false);
const sedangEdit = ref<User | null>(null);
const dialogResetPassword = ref(false);
const sedangResetPassword = ref<User | null>(null);

const form = useForm({
    nama: '',
    email: '',
    password: '',
    no_hp: '',
});

const formResetPassword = useForm({
    password: '',
    password_confirmation: '',
});

function bukaTambah(): void {
    sedangEdit.value = null;
    form.nama = '';
    form.email = '';
    form.password = '';
    form.no_hp = '';
    form.clearErrors();
    dialogTerbuka.value = true;
}

function bukaEdit(item: User): void {
    sedangEdit.value = item;
    form.nama = item.nama;
    form.email = item.email;
    form.password = '';
    form.no_hp = item.no_hp ?? '';
    form.clearErrors();
    dialogTerbuka.value = true;
}

function simpan(): void {
    if (sedangEdit.value) {
        form.put(update.url(sedangEdit.value.id), {
            onSuccess: () => {
                dialogTerbuka.value = false;
            },
        });
    } else {
        form.post(store.url(), {
            onSuccess: () => {
                dialogTerbuka.value = false;
            },
        });
    }
}

function hapus(item: User): void {
    if (confirm(`Hapus admin "${item.nama}"?`)) {
        router.delete(destroy.url(item.id));
    }
}

function bukaResetPassword(item: User): void {
    sedangResetPassword.value = item;
    formResetPassword.password = '';
    formResetPassword.password_confirmation = '';
    formResetPassword.clearErrors();
    dialogResetPassword.value = true;
}

function simpanResetPassword(): void {
    if (!sedangResetPassword.value) return;

    formResetPassword.put(resetPassword.url(sedangResetPassword.value.id), {
        onSuccess: () => {
            dialogResetPassword.value = false;
        },
    });
}
</script>

<template>
    <Head title="Manajemen Admin" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
        <div
            v-if="flash?.success"
            class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700"
        >
            {{ flash.success }}
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <Heading
                title="Manajemen Admin"
                description="Kelola akun administrator yang dapat menyetujui peminjaman dan mengelola data."
            />
            <Button @click="bukaTambah">
                <Plus class="mr-1 size-4" />
                Tambah Admin
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Daftar Admin</CardTitle>
            </CardHeader>
            <CardContent>
                <Table v-if="admins.length > 0">
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>No. HP</TableHead>
                            <TableHead>Peran</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in admins" :key="item.id">
                            <TableCell class="font-medium">{{
                                item.nama
                            }}</TableCell>
                            <TableCell>{{ item.email }}</TableCell>
                            <TableCell>{{ item.no_hp ?? '-' }}</TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary"
                                >
                                    <ShieldCheck class="size-3.5" />
                                    Admin
                                </span>
                            </TableCell>
                            <TableCell class="space-x-1 text-right">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    title="Reset Password"
                                    @click="bukaResetPassword(item)"
                                >
                                    <KeyRound class="size-4" />
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="bukaEdit(item)"
                                >
                                    <Pencil class="size-4" />
                                </Button>
                                <Button
                                    size="sm"
                                    variant="destructive"
                                    @click="hapus(item)"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <p
                    v-else
                    class="py-10 text-center text-sm text-muted-foreground"
                >
                    Belum ada admin.
                </p>
            </CardContent>
        </Card>

        <!-- Dialog tambah/ubah -->
        <Dialog
            :open="dialogTerbuka"
            @update:open="(open) => (dialogTerbuka = open)"
        >
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>
                        {{ sedangEdit ? 'Ubah' : 'Tambah' }} Admin
                    </DialogTitle>
                    <DialogDescription>
                        Admin memiliki akses penuh untuk mengelola armada dan
                        pengajuan.
                    </DialogDescription>
                </DialogHeader>

                <form
                    id="form-admin"
                    class="grid gap-4"
                    @submit.prevent="simpan"
                >
                    <div class="grid gap-2">
                        <Label for="a-nama">Nama lengkap</Label>
                        <Input
                            id="a-nama"
                            v-model="form.nama"
                            required
                        />
                        <InputError :message="form.errors.nama" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="a-email">Email</Label>
                        <Input
                            id="a-email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div v-if="!sedangEdit" class="grid gap-2">
                        <Label for="a-password">Password</Label>
                        <Input
                            id="a-password"
                            v-model="form.password"
                            type="password"
                            required
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="a-nohp">Nomor HP</Label>
                        <Input
                            id="a-nohp"
                            v-model="form.no_hp"
                            type="tel"
                        />
                        <InputError :message="form.errors.no_hp" />
                    </div>
                </form>

                <DialogFooter>
                    <Button
                        variant="outline"
                        :disabled="form.processing"
                        @click="dialogTerbuka = false"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        form="form-admin"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Dialog reset password -->
        <Dialog
            :open="dialogResetPassword"
            @update:open="(open) => (dialogResetPassword = open)"
        >
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Reset Password</DialogTitle>
                    <DialogDescription>
                        Atur ulang password untuk
                        {{ sedangResetPassword?.nama }}.
                    </DialogDescription>
                </DialogHeader>

                <form
                    id="form-reset-password"
                    class="grid gap-4"
                    @submit.prevent="simpanResetPassword"
                >
                    <div class="grid gap-2">
                        <Label for="arp-password">Password baru</Label>
                        <Input
                            id="arp-password"
                            v-model="formResetPassword.password"
                            type="password"
                            required
                        />
                        <InputError :message="formResetPassword.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="arp-password-confirm">Konfirmasi password</Label>
                        <Input
                            id="arp-password-confirm"
                            v-model="formResetPassword.password_confirmation"
                            type="password"
                            required
                        />
                    </div>
                </form>

                <DialogFooter>
                    <Button
                        variant="outline"
                        :disabled="formResetPassword.processing"
                        @click="dialogResetPassword = false"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        form="form-reset-password"
                        :disabled="formResetPassword.processing"
                    >
                        {{ formResetPassword.processing ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
