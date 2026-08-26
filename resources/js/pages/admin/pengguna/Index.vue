<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { User } from '@/types/auth';
import type { Divisi } from '@/types/divisi';
import { store, update, destroy } from '@/routes/manajemen/pengguna';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Manajemen Pengguna', href: '/manajemen-pengguna' },
        ],
    },
});

const props = defineProps<{
    users: User[];
    divisiList: Divisi[];
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const dialogTerbuka = ref(false);
const sedangEdit = ref<User | null>(null);

const form = useForm({
    nama: '',
    email: '',
    password: '',
    no_hp: '',
    divisi_id: '' as string | number,
});

function bukaTambah(): void {
    sedangEdit.value = null;
    form.nama = '';
    form.email = '';
    form.password = '';
    form.no_hp = '';
    form.divisi_id = '';
    form.clearErrors();
    dialogTerbuka.value = true;
}

function bukaEdit(item: User): void {
    sedangEdit.value = item;
    form.nama = item.nama;
    form.email = item.email;
    form.password = '';
    form.no_hp = item.no_hp ?? '';
    form.divisi_id = item.divisi_id ?? '';
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
    if (confirm(`Hapus pengguna "${item.nama}"?`)) {
        router.delete(destroy.url(item.id));
    }
}
</script>

<template>
    <Head title="Manajemen Pengguna" />

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
                title="Manajemen Pengguna"
                description="Tambah, ubah, dan hapus akun pengguna. Akun dibuat oleh admin."
            />
            <Button @click="bukaTambah">
                <Plus class="mr-1 size-4" />
                Tambah Pengguna
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Daftar Pengguna</CardTitle>
            </CardHeader>
            <CardContent>
                <Table v-if="users.length > 0">
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>No. HP</TableHead>
                            <TableHead>Divisi</TableHead>
                            <TableHead>Status Email</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in users" :key="item.id">
                            <TableCell class="font-medium">{{
                                item.nama
                            }}</TableCell>
                            <TableCell>{{ item.email }}</TableCell>
                            <TableCell>{{ item.no_hp ?? '-' }}</TableCell>
                            <TableCell>
                                <Badge v-if="item.divisi" variant="outline">{{
                                    item.divisi.nama_divisi
                                }}</Badge>
                                <span v-else>-</span>
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        item.email_verified_at
                                            ? 'outline'
                                            : 'secondary'
                                    "
                                    :class="
                                        item.email_verified_at
                                            ? 'border-blue-200 bg-blue-50 text-blue-700'
                                            : ''
                                    "
                                >
                                    {{
                                        item.email_verified_at
                                            ? 'Terverifikasi'
                                            : 'Belum'
                                    }}
                                </Badge>
                            </TableCell>
                            <TableCell class="space-x-1 text-right">
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
                    Belum ada pengguna.
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
                        {{ sedangEdit ? 'Ubah' : 'Tambah' }} Pengguna
                    </DialogTitle>
                    <DialogDescription>
                        {{
                            sedangEdit
                                ? 'Perbarui data pengguna.'
                                : 'Akun awal dibuat oleh admin tanpa registrasi mandiri.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <form
                    id="form-pengguna"
                    class="grid gap-4"
                    @submit.prevent="simpan"
                >
                    <div class="grid gap-2">
                        <Label for="u-nama">Nama lengkap</Label>
                        <Input
                            id="u-nama"
                            v-model="form.nama"
                            required
                        />
                        <InputError :message="form.errors.nama" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="u-email">Email</Label>
                        <Input
                            id="u-email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div v-if="!sedangEdit" class="grid gap-2">
                        <Label for="u-password">Password</Label>
                        <Input
                            id="u-password"
                            v-model="form.password"
                            type="password"
                            required
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="u-nohp">Nomor HP</Label>
                        <Input
                            id="u-nohp"
                            v-model="form.no_hp"
                            type="tel"
                        />
                        <InputError :message="form.errors.no_hp" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="u-divisi">Divisi</Label>
                        <Select v-model="form.divisi_id">
                            <SelectTrigger id="u-divisi" class="w-full">
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
                        form="form-pengguna"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
