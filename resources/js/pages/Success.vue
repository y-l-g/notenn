<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineOptions({ layout: PersistentLayout })
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Success'),
        href: '/success',
    },
];
</script>

<template>

    <Head :title="t('Sucess')" />
    <AppLayout :breadcrumbs="breadcrumbs" no-sidebar>
        {{ t('You are successfully subscribed to the Pro plan! Thank you for your support.') }}
    </AppLayout>
</template>
