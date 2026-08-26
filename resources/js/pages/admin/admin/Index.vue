<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, ShieldCheck } from '@lucide/vue';
import { reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
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
import { mockAdminTambahan } from '@/mock/pengguna';
import type { MockPengguna } from '@/mock/pengguna';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Manajemen Admin', href: '/manajemen-admin' }],
    },
});

const admin = ref<MockPengguna[]>(
    [mockAdminTambahan[0]].filter(Boolean).map((p) => ({ ...p })),
);

const dialogTerbuka = ref(false);
const sedangEdit = ref<MockPengguna | null>(null);

const form = reactive({
    nama: '',
    email: '',
    no_hp: '',
});

function bukaTambah(): void {
    sedangEdit.value = null;
    form.nama = '';
    form.email = '';
    form.no_hp = '';
    dialogTerbuka.value = true;
}

function bukaEdit(item: MockPengguna): void {
    sedangEdit.value = item;
    form.nama = item.nama;
    form.email = item.email;
    form.no_hp = item.no_hp ?? '';
    dialogTerbuka.value = true;
}

function simpan(): void {
    if (sedangEdit.value) {
        const target = admin.value.find((p) => p.id === sedangEdit.value?.id);

        if (target) {
            Object.assign(target, {
                nama: form.nama,
                email: form.email,
                no_hp: form.no_hp || null,
            });
        }

        toast.success('Data admin berhasil diperbarui.');
    } else {
        admin.value.push({
            id: Math.max(0, ...admin.value.map((p) => p.id)) + 1,
            nama: form.nama,
            email: form.email,
            no_hp: form.no_hp || null,
            divisi: null,
            role: 'admin',
            email_verified_at: null,
        });
        toast.success('Admin baru berhasil ditambahkan.');
    }

    dialogTerbuka.value = false;
}

function hapus(item: MockPengguna): void {
    admin.value = admin.value.filter((p) => p.id !== item.id);
    toast.info(`Admin ${item.nama} dihapus.`);
}
</script>

<template>
    <Head title="Manajemen Admin" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
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
                <Table v-if="admin.length > 0">
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
                        <TableRow v-for="item in admin" :key="item.id">
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
                    Belum ada admin tambahan.
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
                    <DialogTitle
                        >{{ sedangEdit ? 'Ubah' : 'Tambah' }} Admin</DialogTitle
                    >
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
                        <Input id="a-nama" v-model="form.nama" required />
                    </div>

                    <div class="grid gap-2">
                        <Label for="a-email">Email</Label>
                        <Input
                            id="a-email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="a-nohp">Nomor HP</Label>
                        <Input id="a-nohp" v-model="form.no_hp" type="tel" />
                    </div>
                </form>

                <DialogFooter>
                    <Button variant="outline" @click="dialogTerbuka = false"
                        >Batal</Button
                    >
                    <Button type="submit" form="form-admin">Simpan</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
