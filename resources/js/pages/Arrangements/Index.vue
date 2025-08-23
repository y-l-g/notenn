<script setup lang="ts">
import { useArrangementIndex } from '@/Stores/arrangementIndex';
import Pagination from '@/components/app/Pagination.vue';
import TunebookCommentDialog from '@/components/app/TunebookCommentDialog.vue';
import ArrangementFilters from '@/components/app/arrangement-index/ArrangementFilters.vue';
import ArrangementList from '@/components/app/arrangement-index/ArrangementList.vue';
import CommentItem from '@/components/app/arrangement-show/CommentItem.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { ArrangementFromMeilisearch, BreadcrumbItem, MeilisearchPaginatedResponse, PaginatedResponse, Comment, Tunebook } from '@/types';
import { Head, InertiaForm, router, useForm, usePage } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps<{
    arrangements: MeilisearchPaginatedResponse<ArrangementFromMeilisearch>;
    tunebook?: Tunebook;
    comments?: PaginatedResponse<Comment>
    tunebook_likes?: number;
    rhythms: Array<{ id: number; name: string }>;
    origins: Array<{ id: number; name: string }>;
    composers: Array<{ id: number; name: string }>;
    filters?: {
        search?: string;
        rhythm?: string;
        composer?: string;
        origin?: string;
        user?: string
        best?: boolean
        tunebook?: string
        userLike?: boolean
        onlyUser?: boolean
    };
}>();

defineOptions({ layout: PersistentLayout })
const title = () => {
    if (props.filters?.tunebook) {
        return t('Tunebook ') + props.filters?.tunebook
    }
    return t('Tunes')

}
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: title(),
        href: '/arrangements',
    },
];

const page = usePage();

useArrangementIndex().arrangementIds = props.arrangements?.data?.hits.map(arr => arr.id);

const open = ref(false);

const form = useForm({
    content: '',
    is_suggestion: false as boolean,
    tunebook_id: props.tunebook?.id,
});

const submitComment = (form: InertiaForm<any>) => {
    form.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            open.value = false;
        },
    });
};

const onPageChange = (page: number) => {
    router.get(
        props.arrangements.path,
        {
            page: page,
            search: props.filters?.search,
            rhythm: props.filters?.rhythm,
            composer: props.filters?.composer,
            origin: props.filters?.origin,
            user: props.filters?.user,
            best: props.filters?.best,
            tunebook: props.filters?.tunebook,
            userLike: props.filters?.userLike,
            onlyUser: props.filters?.onlyUser
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const onCommentPageChange = (page: number) => {
    router.get(
        props.arrangements.path,
        {
            comments: page,
            search: props.filters?.search,
            rhythm: props.filters?.rhythm,
            composer: props.filters?.composer,
            origin: props.filters?.origin,
            user: props.filters?.user,
            best: props.filters?.best,
            tunebook: props.filters?.tunebook,
            userLike: props.filters?.userLike,
            onlyUser: props.filters?.onlyUser
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};



</script>

<template>

    <Head :title="t('Explore Tunes')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between gap-4">
            <Heading>
                <span>{{ title() }}</span>
            </Heading>
            <div v-if="filters?.tunebook" class="flex items-center gap-2">
                <span class="mb-4">{{ tunebook_likes }}</span>
                <Heart v-if="!page.props.auth?.user" class="size-4 mb-4" />
                <Button v-if="filters?.tunebook && page.props.auth?.user" variant="ghost" size="icon"
                    class="mb-4 cursor-pointer"
                    @click="router.post(route('like', { likeable_id: tunebook?.id, likeable_type: 'App\\Models\\Tunebook' }))">
                    <Heart />
                </Button>
            </div>
        </div>
        <ArrangementFilters :search="filters?.search" :rhythm="filters?.rhythm" :origin="filters?.origin"
            :composer="filters?.composer" :rhythms="rhythms" :origins="origins" :composers="composers"
            :userLike="filters?.userLike" :onlyUser="filters?.onlyUser" :best="filters?.best"
            :tunebook="filters?.tunebook" />
        <ArrangementList :arrangements="arrangements.data.hits" :tunebook="tunebook" />
        <Pagination :data="arrangements" @pageChange="onPageChange" />
        <div v-if="tunebook?.id && comments?.data.length">
            <div class="space-y-4">
                <Heading>{{ comments.total }} {{ t('comment', comments.total) }}</Heading>
                <TunebookCommentDialog v-if="page.props.auth.user" v-model:form="form" v-model:open="open"
                    @submit="submitComment" />
                <CommentItem v-for="comment in comments.data" :key="comment.id" :comment="comment" />
                <Pagination :data="comments" @pageChange="onCommentPageChange" />
            </div>

        </div>
        <template #sidebar>
            <div class="sticky top-20 space-y-1">
                <a v-for="arrangement in arrangements.data.hits" :key="arrangement.id"
                    class="block px-2 py-1 rounded cursor-pointer"
                    :class="useArrangementIndex().activeItemId === arrangement.id ? 'font-medium' : ''"
                    @click="useArrangementIndex().scrollToItem(arrangement.id)">
                    {{ arrangement.tune.title }}
                    <span v-if="arrangement.name">{{ - arrangement.name }}</span>
                </a>
            </div>
        </template>
    </AppLayout>
</template>
