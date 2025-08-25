<script setup lang="ts">
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
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
        <Heading>{{ t('Legal Notices') }}</Heading>

        <HeadingSmall>{{ t('Site Editor') }}</HeadingSmall>
        <p>{{ t('Youenn LE GOUEDEC Individual Entrepreneur') }}</p>
        <p>youenn@legouedec.fr</p>

        <HeadingSmall>{{ t('Hosting') }}</HeadingSmall>
        <p>{{ t('Hetzner') }}</p>
        <p>{{ t('Gunzenhausen, Germany') }}</p>

        <HeadingSmall>{{ t('Personal Data') }}</HeadingSmall>
        <p class="font-medium">{{ t('Data collection') }}</p>
        <p>{{ t('The collected data (email, name, IP address) is used for:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Managing user accounts') }}</li>
            <li>{{ t('Processing payments via Stripe') }}</li>
            <li>{{ t('Adapting prices based on currency/region') }}</li>
            <li>{{ t('Analyzing site usage (Cookies)') }}</li>
        </ul>
        <p class="font-medium">{{ t('GDPR Rights') }}</p>
        <p>{{ t('You can:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Access your data') }}</li>
            <li>{{ t('Rectify them') }}</li>
            <li>{{ t('Delete them') }}</li>
            <li>{{ t('Object to their processing') }}</li>
        </ul>

        <p class="font-medium">{{ t('Cookies') }}</p>
        <p>{{ t('The site uses cookies for:') }}</p>

        <ul class="list-disc pl-5">
            <li>{{ t('Authentication') }}</li>
            <li>{{ t('Analytics (Google Analytics, etc.)') }}</li>
            <li>{{ t('Stripe functionalities') }}</li>
        </ul>

        <HeadingSmall>{{ t('Payments & Financial Conditions') }}</HeadingSmall>
        <p class="font-medium">{{ t('Notenn Pro') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('One-time secure payment via Stripe') }}</li>
            <li>{{ t('Variable prices depending on:') }}
                <ul class="list-disc pl-5">
                    <li>{{ t('Currency (EUR, USD, etc.)') }}</li>
                    <li>{{ t('Region (automatic detection)') }}</li>
                </ul>
            </li>
            <li>{{ t('Stripe collects banking data (not stored on our servers)') }}</li>
            <li><a href="https://stripe.com/legal/ssa">{{ t('Link to Stripe Terms of Service') }}</a></li>
            <li>{{ t('No refund (digital service immediately accessible)') }}</li>
        </ul>

        <p class="font-medium">{{ t('Claims and disputes') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t("Please contact me to find a friendly solution") }}</li>
            <li>youenn@legouedec.fr</li>
        </ul>

        <HeadingSmall>{{ t('Content & Intellectual Property') }}</HeadingSmall>
        <p class="font-medium">{{ t('YouTube Videos') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Embedded via YouTube API (privacy-enhanced mode)') }}</li>
            <li>{{ t('Subject to YouTube Terms of Service') }}</li>
        </ul>

        <p class="font-medium">{{ t('Copyright') }}</p>
        <p>{{ t('If you find content that belongs to you against your wish, please contact us') }}</p>

        <p class="font-medium">{{ t('Licence') }}</p>
        <p>{{ t('Most of the initial database comes from') }} <a href="https://thesession.org/">thesession.org</a>.</p>
        <p><a href="https://github.com/adactio/TheSession-data">{{ t('Link of the original database') }}</a></p>
        <p><a href="https://github.com/y-l-g/notenn-data">{{ t('Link of the database used on this site') }}</a></p>

        <p><em>{{ t('Last updated:') }} {{ (new Date(Date.UTC(2025, 8, 25))).toLocaleDateString(useI18n().locale.value)
        }}</em></p>
    </AppLayout>
</template>
