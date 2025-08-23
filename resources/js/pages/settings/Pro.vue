<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { User, type BreadcrumbItem } from '@/types';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import { useI18n } from 'vue-i18n';
import Button from '@/components/ui/button/Button.vue';

const { t } = useI18n();
const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Pro Plan'),
        href: '/settings/pro',
    },
];
const page = usePage();
const user = page.props.auth.user as User;

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" no-sidebar>

        <Head title="Pro Plan" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall>
                    {{ t('Your Account') }}
                </HeadingSmall>
                <div v-if="user.is_pro">
                    <p class="mb-2">
                        {{ t('Congratulations! You subscribed to Pro Plan and have access to all features!') }}
                    </p>
                </div>
                <div v-else>
                    <p class="mb-2">
                        {{ t("You subscribed to a free account and you can't access premium feature") }}
                    </p>
                    <Button>
                        <a :href="route('checkout')">
                            {{ t('Subscribe to Pro Plan') }}
                        </a>
                    </Button>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
