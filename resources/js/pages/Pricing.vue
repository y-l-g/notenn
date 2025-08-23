<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Check, X, Zap, Gem } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';

defineProps<{
    formattedPrice: string;
    remainingCoupons: number;
}>();

const { t } = useI18n();
const isUserPro = usePage().props.auth.user?.is_pro;

const features = [
    { name: t('Access to all public tunes and Tunebooks'), free: true, pro: true },
    { name: t('Listen tunes'), free: true, pro: true },
    { name: t('Create new tunes'), free: true, pro: true },
    { name: t('Custom instruments'), free: false, pro: true },
    { name: t('PDF export for tunes and tunebooks'), free: false, pro: true, soon: true },
    { name: t('Create private tunes and tunebooks'), free: false, pro: true, soon: true },
    { name: t('Make tunes and tunebooks collaborative'), free: false, pro: true, soon: true },
    { name: t('Transpose tunes'), free: false, pro: true, soon: true },
    { name: t('Custom drum styles'), free: false, pro: true, soon: true },
    { name: t('Custom accompaniement styles'), free: false, pro: true },
    { name: t('Add an audio recording for an arrangement'), free: false, pro: true, soon: true },
    { name: t('Export tune to midi'), free: false, pro: true, soon: true },
    { name: t('Priorized feature requests'), free: false, pro: true },
    // { name: t('Private access to Github Repo'), free: false, pro: true },
    { name: t('Tabs for guitar, ukulele, mandolin... '), free: false, pro: true, soon: true },
];


defineOptions({ layout: PersistentLayout })
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Pricing'),
        href: '/pricing',
    },
];
</script>

<template>

    <Head :title="t('Pricing')" />
    <AppLayout :breadcrumbs="breadcrumbs" no-sidebar>
        <div class="mx-auto max-w-4xl py-6 px-4">
            <h2 class="text-3xl font-bold text-center mb-2">{{ t('Plans') }}</h2>
            <!-- <p class="text-center text-muted-foreground mb-2">{{ t('Unlock all the features') }}</p> -->
            <p class="text-center text-muted-foreground mb-2">
                {{ t('50% discount for the first 100 paying users with code ') }}<strong>NOTENN1</strong>
            </p>
            <p class="text-center text-muted-foreground mb-2">
                {{ t('100% Free ') }}<strong>FREENOTENN</strong>
            </p>
            <p class="text-center text-muted-foreground mb-8">
                {{ t('Remaining coupons: ') }}<strong>{{ remainingCoupons }}</strong>
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border rounded-xl p-6 bg-background relative">
                    <div class="flex items-center gap-2 mb-4">
                        <Zap class="w-5 h-5 text-yellow-500" />
                        <h3 class="text-xl font-semibold">{{ t('Free') }}</h3>
                    </div>

                    <p class="text-4xl font-bold mb-2">{{ t('Free') }}<span
                            class="text-sm font-normal text-muted-foreground">{{ t('/Forever') }}</span></p>

                    <ul class="space-y-3 mb-8 mt-6">
                        <li v-for="feature in features" :key="feature.name" class="flex items-start">
                            <component :is="feature.free ? Check : X" class="size-[20px] mr-2 flex-shrink-0"
                                :class="feature.free ? 'text-green-500' : 'text-gray-400'" />
                            <span :class="{ 'text-muted-foreground': !feature.free }">{{ feature.name }}</span>
                        </li>
                    </ul>
                </div>

                <div class="border-2 border-primary rounded-xl p-6 bg-background relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 bg-primary text-primary-foreground px-3 py-1 text-xs font-bold rounded-bl-xl">
                        {{ t('RECOMMENDED') }}
                    </div>

                    <div class="flex items-center gap-2 mb-4">
                        <Gem class="w-5 h-5 text-primary" />
                        <h3 class="text-xl font-semibold">{{ t('Pro') }}</h3>
                    </div>

                    <p class="text-4xl font-bold mb-2">
                        {{ formattedPrice }}<span class="text-sm font-normal text-muted-foreground">{{ t('/One Time')
                        }}</span></p>
                    <ul class="space-y-3 mb-8 mt-6">
                        <li v-for="feature in features" :key="feature.name" class="flex items-start">
                            <component :is="feature.pro ? Check : X" class="size-[20px] mr-2 flex-shrink-0"
                                :class="feature.pro ? 'text-green-500' : 'text-gray-400'" />
                            <span :class="{ 'text-muted-foreground': !feature.pro }">{{ feature.name }}<template
                                    v-if="feature.soon">{{ " " }}{{ t('(soon)') }}</template></span>
                        </li>
                    </ul>
                    <Button v-if="!isUserPro" as-child variant="outline" class="w-full">
                        <a :href="route('checkout')">

                            {{ t('Get Pro') }}
                        </a>
                    </Button>
                    <Button v-else as-child variant="outline" class="w-full">
                        <div>
                            {{ t('You already subscribed to Pro') }}
                        </div>
                    </Button>

                </div>
            </div>

            <p class=" text-center text-sm text-muted-foreground mt-8">
                {{ t('Cancel anytime. No hidden fees.') }}<br>
                {{ t('All transactions are securely processed by Stripe.') }}
            </p>
        </div>
    </AppLayout>
</template>
