<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { useMatchUrl } from '@/composables/useMatchUrl';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Settings } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ t('Tunes') }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="useMatchUrl().isMatchingUrl(page.url, item.href)"
                    :tooltip="item.title">
                    <Link :href="item.href" prefetch>
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            <SidebarMenuItem v-if="page.props.auth?.user?.id === 1">
                <SidebarMenuButton as-child :is-active="'/admin-dashboard' === page.url" tooltip="Admin Dashboard">
                    <Link href="/admin-dashboard" prefetch>
                    <Settings />
                    <span>{{ t('Admin Dashboard') }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
