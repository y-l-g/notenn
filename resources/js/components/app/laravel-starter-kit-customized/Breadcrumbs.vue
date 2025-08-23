<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/vue3';

interface BreadcrumbItemType {
    title: string;
    href?: string;
}

defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList class="flex-nowrap">
            <template v-for="(item, index) in breadcrumbs" :key="index">
                <BreadcrumbItem class="text-nowrap max-w-[90px] sm:max-w-[150px] lg:max-w-[250px] xl:max-w-[320px]">
                    <template v-if="index === breadcrumbs.length - 1">
                        <BreadcrumbPage class="truncate">{{ item.title }}</BreadcrumbPage>
                    </template>
                    <template v-else>
                        <BreadcrumbLink as-child>
                            <Link class="truncate" :href="item.href ?? '#'" prefetch>{{ item.title }}</Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index !== breadcrumbs.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
