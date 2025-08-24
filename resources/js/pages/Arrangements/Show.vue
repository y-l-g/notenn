<script setup lang="ts">
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { useAbcNotation } from '@/composables/useAbcNotation';
import AppLayout from '@/layouts/AppLayout.vue';
import { Arrangement, BreadcrumbItem, Comment, PaginatedResponse, Tunebook } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Heart, Link as LinkIcon } from 'lucide-vue-next';
import { useComments } from '@/composables/useComments';
import { useWindowSize } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import Pagination from '@/components/app/Pagination.vue';
import Render from '@/components/app/abcjs/Render.vue';
import ArrangementActions from '@/components/app/arrangement-show/ArrangementActions.vue';
import OtherArrangementsList from '@/components/app/arrangement-show/OtherArrangementsList.vue';
import CommentDialog from '@/components/app/arrangement-show/CommentDialog.vue';
import CommentItem from '@/components/app/arrangement-show/CommentItem.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import TunePlayer from '@/components/app/TunePlayer.vue';
import { usePlayerStore } from '@/Stores/player';
import { onMounted, ref } from 'vue';
import { renderAbc, TuneObject } from 'abcjs';
import AccordionCustom from '@/components/app/AccordionCustom.vue';
const { t } = useI18n();
const props = defineProps<{
    arrangement: Arrangement;
    other_arrangements: Array<Arrangement>;
    user_like_arrangement: boolean;
    comments: PaginatedResponse<Comment>;
    tunebooks: Tunebook[];
    suggestion?: boolean;
    user_arrangement_for_this_tune?: Arrangement
}>();

defineOptions({ layout: PersistentLayout })

const playerStore = usePlayerStore();

const { width } = useWindowSize()

const { open, form, submitComment } = useComments(props.arrangement.id);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.arrangement.tune.title,
        href: route('arrangements.show', {
            arrangement: props.arrangement.id,
        }),
    },
    {
        title: props.arrangement.name ?? `${t('Added by')} ${props.arrangement.user?.name ?? t('Santa Claus')}`,
        href: route('arrangements.show', {
            arrangement: props.arrangement.id,
        }),
    },
];

const pageData = usePage();

const onPageChange = (page: number) => {
    router.get(
        props.comments.path,
        {
            page: page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
const visualObj = ref<TuneObject>(renderAbc('*', '')[0])
onMounted(() => {
    visualObj.value = playerStore.synthControl.visualObj
    if (props.suggestion) {
        open.value = true
        form.is_suggestion = true
        form.suggestion_type = 'tune'
    }
}
)
</script>

<template>

    <Head :title="arrangement.tune.title" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>
            {{ arrangement.tune.title }}
        </Heading>

        <Render :abcString="useAbcNotation(arrangement).abcNotationWithoutWordsAndNotesAndTranscription" setTune />
        <div class="flex justify-between items-center border-b border-t py-1">
            <TunePlayer :tune-obj="visualObj" :is-current="true" />
            <a target="_blank" rel="noopener noreferrer"
                class="flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                <LinkIcon class="size-4" />
                <span>{{ t('Source') }}</span>
            </a>
            <ArrangementActions v-if="pageData.props.auth.user" :arrangement="arrangement" :tunebooks="tunebooks"
                :user_arrangement_for_this_tune />
            <Link v-if="pageData.props.auth.user" prefetch
                :href="route('like', { likeable_id: arrangement.id, likeable_type: 'App\\Models\\Arrangement' })"
                method="post" :preserve-scroll="true">
            <p class="flex items-center gap-1 text-sm cursor-pointer">
                {{ arrangement.likes_count }}
                <Heart class="size-4" />
            </p>
            </Link>
            <p v-else class="flex items-center gap-1 text-sm">
                {{ arrangement.likes_count }}
                <Heart class="size-4" />
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <Badge v-for="tag in arrangement.tags" :key="tag.id" variant="secondary">
                {{ tag.name }}
            </Badge>
        </div>
        <AccordionCustom :arrangement="arrangement" type="ABC Notation" />
        <div v-if="other_arrangements.length > 0" v-show="width < usePlayerStore().breakPointSidebar">
            <HeadingSmall>{{ t('Other Arrangements for this tune') }}</HeadingSmall>
            <OtherArrangementsList :arrangements="other_arrangements" :tune="arrangement.tune"
                :user="arrangement.user" />
        </div>
        <div class="space-y-2">
            <Heading>{{ comments.total }} {{ t('comment', comments.total) }}</Heading>
            <CommentDialog v-if="pageData.props.auth.user" v-model:form="form" v-model:open="open"
                @submit="submitComment" />
            <div class="space-y-4">
                <CommentItem v-for="comment in comments.data" :key="comment.id" :comment="comment" />
                <Pagination :data="comments" @pageChange="onPageChange" />
            </div>
        </div>
        <template #sidebar>
            <div class="space-y-4" v-if="other_arrangements.length > 0">
                <Heading class="text-lg font-medium">{{ t('Other Arrangements for this tune') }}</Heading>
                <OtherArrangementsList :arrangements="other_arrangements" :tune="arrangement.tune"
                    :user="arrangement.user" />
            </div>
        </template>
    </AppLayout>
</template>
