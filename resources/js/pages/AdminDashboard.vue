<script setup lang="ts">
import Description from '@/components/app/Description.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import ListItem from '@/components/app/ListItem.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { Arrangement, Comment, Composer, PaginatedResponse, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineOptions({ layout: PersistentLayout })
defineProps<{
    tuneModificationSuggestions?: Comment[];
    composersWithoutTunes?: Composer[]
    arrangementsWithoutLikesAndWithoutUser?: PaginatedResponse<Arrangement>
    tunePairs?: Record<string, {
        tune_1: {
            id: number
            title: string
        }
        tune_2: {
            id: number
            title: string
        }
    }>
    composerPairs?: Record<string, {
        composer_1: {
            id: number
            name: string
        }
        composer_2: {
            id: number
            name: string
        }
    }>

}>();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Admin Dashboard'),
        href: '/admin-dashboard',
    },
];
</script>

<template>

    <Head :title="t('Admin Dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>
            {{ t('Admin Dashboard') }}
        </Heading>
        <HeadingSmall>{{ t('Modifications requests') }}</HeadingSmall>
        <ul class="space-y-5" v-if="tuneModificationSuggestions && tuneModificationSuggestions.length">
            <ListItem v-for="suggestion in tuneModificationSuggestions" :key="suggestion.id" class="space-y-2">
                <p>{{ suggestion.content }}</p>
                <Description>On
                    <!-- <Link :href="route('arrangements.show', { tune: suggestion.commentable.best_arrangement.id })">
                    </Link> -->
                    {{ suggestion.commentable.title }}
                </Description>
            </ListItem>
        </ul>
        <HeadingSmall>{{ t('Composers without tunes') }}</HeadingSmall>
        <ul v-if="composersWithoutTunes && composersWithoutTunes.length">
            <li v-for="composer in composersWithoutTunes" :key="composer.id">{{ composer.name }}</li>
        </ul>
        <HeadingSmall>{{ t('Arrangements without likes') }}</HeadingSmall>
        <ul v-if="arrangementsWithoutLikesAndWithoutUser && arrangementsWithoutLikesAndWithoutUser.data.length">
            <li v-for="arrangement in arrangementsWithoutLikesAndWithoutUser.data" :key="arrangement.id">
                {{ arrangement.tune.title }}</li>
        </ul>
        <HeadingSmall>{{ t('Tunes with similar titles') }}</HeadingSmall>
        <ul v-if="tunePairs">
            <li v-for="pair in Object.values(tunePairs)" :key="pair.tune_1.id + '-' + pair.tune_2.id">
                {{ pair.tune_1.title }} ↔ {{ pair.tune_2.title }}
            </li>
        </ul>
        <HeadingSmall>{{ t('Composers with similar names') }}</HeadingSmall>
        <ul v-if="composerPairs">
            <li v-for="pair in Object.values(composerPairs)" :key="pair.composer_1.id + '-' + pair.composer_2.id">
                {{ pair.composer_1.name }} ↔ {{ pair.composer_2.name }}
            </li>
        </ul>
    </AppLayout>
</template>
