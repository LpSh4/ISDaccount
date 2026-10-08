<script setup lang="ts">
import {Link, usePage} from '@inertiajs/vue3';
import {BookOpen, FolderGit2, LayoutGrid, ShieldAlert} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
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
import {admindashboard, dashboard} from '@/routes';
import type { NavItem } from '@/types';
import {computed} from "vue";

const page = usePage();
const user = computed(() => page.props.auth?.user as any);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Achievements',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];
    // Prevent admin dashboard from rendering if the user isnt an admin
    if (user.value?.role === 'admin') {
        items.push({
            title: 'Admin Dashboard',
            href: admindashboard(),
            icon: ShieldAlert,
        });
    }

    return items;
});

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Repository',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: FolderGit2,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
<!--            <NavFooter :items="footerNavItems" />-->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
