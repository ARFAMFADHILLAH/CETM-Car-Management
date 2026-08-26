<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { reactive, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
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
import { mockDivisi, mockPengguna } from '@/mock/pengguna';
import type { MockPengguna } from '@/mock/pengguna';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Manajemen Pengguna', href: '/manajemen-pengguna' },
        ],
    },
});

const pengguna = ref<MockPengguna[]>(
    mockPengguna.filter((p) => p.role === 'user').map((p) => ({ ...p })),
);

const dialogTerbuka = ref(false);
const sedangEdit = ref<MockPengguna | null>(null);

const form = reactive({
    nama: '',
    email: '',
    no_hp: '',
    divisi: '',
});

function bukaTambah(): void {
    sedangEdit.value = null;
    form.nama = '';
    form.email = '';
    form.no_hp = '';
    form.divisi = '';
    dialogTerbuka.value = true;
}

function bukaEdit(item: MockPengguna): void {
    sedangEdit.value = item;
    form.nama = item.nama;
    form.email = item.email;
    form.no_hp = item.no_hp ?? '';
    form.divisi = item.divisi ?? '';
    dialogTerbuka.value = true;
}

function simpan(): void {
    if (sedangEdit.value) {
        const target = pengguna.value.find(
            (p) => p.id === sedangEdit.value?.id,
        );

        if (target) {
            Object.assign(target, {
                nama: form.nama,
                email: form.email,
                no_hp: form.no_hp || null,
                divisi: form.divisi || null,
            });
        }

        toast.success('Data pengguna berhasil diperbarui.');
    } else {
        pengguna.value.push({
            id: Math.max(0, ...pengguna.value.map((p) => p.id)) + 1,
            nama: form.nama,
            email: form.email,
            no_hp: form.no_hp || null,
            divisi: form.divisi || null,
            role: 'user',
            email_verified_at: null,
        });
        toast.success('Pengguna baru berhasil ditambahkan.');
    }

    dialogTerbuka.value = false;
}

function hapus(item: MockPengguna): void {
    pengguna.value = pengguna.value.filter((p) => p.id !== item.id);
    toast.info(`Pengguna ${item.nama} dihapus.`);
}
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
    >
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
                <Table v-if="pengguna.length > 0">
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
                        <TableRow v-for="item in pengguna" :key="item.id">
                            <TableCell class="font-medium">{{
                                item.nama
                            }}</TableCell>
                            <TableCell>{{ item.email }}</TableCell>
                            <TableCell>{{ item.no_hp ?? '-' }}</TableCell>
                            <TableCell>
                                <Badge v-if="item.divisi" variant="outline">{{
                                    item.divisi
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
                                            ? 'border-green-200 bg-green-50 text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-400'
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
                        <Input id="u-nama" v-model="form.nama" required />
                    </div>

                    <div class="grid gap-2">
                        <Label for="u-email">Email</Label>
                        <Input
                            id="u-email"
                            v-model="form.email"
                            type="email"
                            required
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="u-nohp">Nomor HP</Label>
                        <Input id="u-nohp" v-model="form.no_hp" type="tel" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="u-divisi">Divisi</Label>
                        <Select v-model="form.divisi">
                            <SelectTrigger id="u-divisi" class="w-full">
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
                </form>

                <DialogFooter>
                    <Button variant="outline" @click="dialogTerbuka = false"
                        >Batal</Button
                    >
                    <Button type="submit" form="form-pengguna">Simpan</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
