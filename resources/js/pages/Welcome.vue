<script setup lang="ts">
import Description from '@/components/app/Description.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import StatsCard from '@/components/app/StatsCard.vue';
import { ArrangementFromMeilisearch, BreadcrumbItem, Comment, TunebookFromMeilisearch } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useRelativeTime } from '@/composables/useFormatDate';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import ListItem from '@/components/app/ListItem.vue';
import { User2 } from 'lucide-vue-next';
defineOptions({ layout: PersistentLayout })
const { t } = useI18n();

defineProps<{
    latestArrangements: { hits: ArrangementFromMeilisearch[] };
    tunesMostLiked: { hits: ArrangementFromMeilisearch[] };
    tunebooksMostLiked: { hits: TunebookFromMeilisearch[] };
    irishTunes: { hits: ArrangementFromMeilisearch[] };
    latestComments: Comment[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Welcome'),
        href: '/',
    },
];
const page = usePage()
</script>

<template>

    <Head :title="t('Welcome')" />
    <AppLayout :breadcrumbs="breadcrumbs" no-sidebar>
        <Heading>
            {{ t('Welcome') }} <span v-if="page.props.auth.user">{{ page.props.auth.user.name }}</span>
        </Heading>

        <div class="columns-1 lg:columns-2 gap-10 space-y-10">
            <StatsCard title="Arrangements added recently" :items="latestArrangements.hits.map(t => ({
                id: t.id,
                likes_count: t.likes_count,
                title: t.title,
                created_at: t.created_at,
                link: route('arrangements.show', { arrangement: t.id })
            }))" />

            <StatsCard title="Arrangements most liked" :items="tunesMostLiked.hits.map(t => ({
                id: t.id,
                title: t.tune.title,
                likes_count: t.likes_count,
                created_at: t.created_at,
                link: route('arrangements.show', { arrangement: t.id })
            }))" />

            <StatsCard title="Tunebooks most liked" :items="tunebooksMostLiked.hits.map(t => ({
                id: t.id,
                title: t.name,
                likes_count: t.likes_count,
                created_at: t.created_at,
                link: route('arrangements.index', { tunebook: t.name, user: '', best: false })
            }))" />

            <StatsCard v-if="irishTunes.hits.length > 0" title="Irish Tunes" :items="irishTunes.hits.map(t => ({
                id: t.id,
                title: t.tune.title,
                likes_count: t.likes_count,
                created_at: t.created_at,
                link: route('arrangements.show', { arrangement: t.id })
            }))" />

            <div class="break-inside-avoid-column">
                <HeadingSmall>Latest Comments</HeadingSmall>
                <div class="space-y-4">
                    <ListItem v-for="comment in latestComments" :key="comment.id">
                        <p class="font-medium flex items-center gap-2">
                            <User2 class="size-4" />{{ comment.user.name }}
                        </p>
                        <p class="">"{{ comment.content.substring(0, 150) }}{{ comment.content.length < 150 ? "" : "..."
                                }}"</p>
                                <div class="flex justify-between gap-6 items-end">
                                    <div>

                                        <Description>
                                            <Link class=""
                                                :href="comment.commentable_type === 'App\\Models\\Arrangement' ?
                                                    route('arrangements.show', { arrangement: comment.commentable_id }) :
                                                    comment.commentable_type === 'App\\Models\\Tune' ?
                                                        route('tunes.show', { tune: comment.commentable.id }) :
                                                        route('arrangements.index', { tunebook: comment.commentable.name })">
                                            <Description>
                                                {{
                                                    comment.commentable_type === 'App\\Models\\Arrangement' ?
                                                        comment.commentable.tune.title + t(' - Arrangement') :
                                                        comment.commentable_type === 'App\\Models\\Tune' ?
                                                            comment.commentable.title + t(' - Tune') :
                                                            comment.commentable.name + t(' - Tunebook')
                                                }}
                                            </Description>
                                            </Link>
                                        </Description>
                                    </div>
                                    <Description class="text-nowrap">
                                        {{ useRelativeTime().relativeTime(comment.created_at) }}
                                    </Description>
                                </div>
                    </ListItem>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
