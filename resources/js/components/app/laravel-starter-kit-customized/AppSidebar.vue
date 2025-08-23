<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard, Library, Music, Plus } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavMain from './NavMain.vue';
import NavFooter from '@/components/app/laravel-starter-kit-customized/NavFooter.vue';
import NavUser from '@/components/laravel-starter-kit/NavUser.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const page = usePage()

const mainNavItems: NavItem[] = [
    {
        title: t('Tunes'),
        href: route('arrangements.index', { best: "true", tunebook: "", user: "" }),
        icon: Music,
    },
    {
        title: t('Tunebooks'),
        href: route('tunebooks.index'),
        icon: Library,
    },
];
const authNavItems: NavItem[] = page.props.auth?.user?.id ?
    [
        ...mainNavItems,
        {
            title: t('Create Tune'),
            href: route('tunes.create'),
            icon: Plus,
        },
        {
            title: t('Dashboard'),
            href: route('dashboard'),
            icon: LayoutDashboard,
        },

    ] :
    [];

const footerNavItems: NavItem[] = [
];


</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('home')" prefetch>
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="page.props.auth.user?.id ? authNavItems : mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser v-if="page.props.auth.user?.id" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
