<script setup lang="ts">
import AppSidebar from '@/components/app/laravel-starter-kit-customized/AppSidebar.vue';
import AppContent from '@/components/laravel-starter-kit/AppContent.vue';
import AppShell from '@/components/laravel-starter-kit/AppShell.vue';
import AppSidebarHeader from '@/components/app/laravel-starter-kit-customized/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    noSidebar?: boolean
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <!-- <BetaBanner /> -->
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar"
            class="md:peer-data-[variant=inset]:shadow-none md:peer-data-[variant=inset]:border overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div :class="['max-w-[100vw], min-w-0  grid grid-cols-1 h-full', {
                'xl:grid-cols-[1fr_370px] ': !noSidebar
            }]">
                <div :class="['space-y-5 p-2 sm:p-4 md:p-6 xl:p-8', {
                    'xl:border-r': !noSidebar
                }]">
                    <slot />
                </div>
                <div v-if="$slots.sidebar" class="hidden min-w-0 xl:block space-y-5 p-4 pt-6">
                    <slot name="sidebar" />
                </div>
            </div>
        </AppContent>
    </AppShell>
</template>
