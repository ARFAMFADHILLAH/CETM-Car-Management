<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Bot,
    CalendarDays,
    Car,
    ClipboardCheck,
    ClipboardList,
    FilePlus2,
    LayoutGrid,
    UserCog,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as approval } from '@/routes/approval';
import { show as chatbot } from '@/routes/chatbot';
import { index as jadwal } from '@/routes/jadwal';
import {
    admin as manajemenAdmin,
    pengguna as manajemenPengguna,
} from '@/routes/manajemen';
import { index as mobil } from '@/routes/mobil';
import { index as notifikasi } from '@/routes/notifikasi';
import {
    create as peminjamanCreate,
    index as peminjamanIndex,
} from '@/routes/peminjaman';
import type { NavItem } from '@/types';

const page = usePage();

const isAdmin = computed(() => page.props.auth.user?.role?.role === 'admin');

const mainNavItems = computed<NavItem[]>(() =>
    isAdmin.value
        ? [
              { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
              {
                  title: 'Approve Peminjaman',
                  href: approval(),
                  icon: ClipboardCheck,
              },
              { title: 'Jadwal Mobil', href: jadwal(), icon: CalendarDays },
              { title: 'Data Mobil', href: mobil(), icon: Car },
              {
                  title: 'Manajemen Pengguna',
                  href: manajemenPengguna(),
                  icon: Users,
              },
              {
                  title: 'Manajemen Admin',
                  href: manajemenAdmin(),
                  icon: UserCog,
              },
          ]
        : [
              { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
              {
                  title: 'Ajukan Peminjaman',
                  href: peminjamanCreate(),
                  icon: FilePlus2,
              },
              {
                  title: 'Daftar Pengajuan',
                  href: peminjamanIndex(),
                  icon: ClipboardList,
              },
              { title: 'Jadwal Mobil', href: jadwal(), icon: CalendarDays },
              { title: 'Data Mobil', href: mobil(), icon: Car },
          ],
);

const footerNavItems = computed<NavItem[]>(() => [
    { title: 'Notifikasi', href: notifikasi(), icon: Bell },
    { title: 'Chatbot', href: chatbot(), icon: Bot },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" label="Menu Utama" />
            <NavMain :items="footerNavItems" label="Lainnya" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
