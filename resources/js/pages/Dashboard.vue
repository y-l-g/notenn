<script setup lang="ts">
import Description from '@/components/app/Description.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import ListItem from '@/components/app/ListItem.vue';
import Pagination from '@/components/app/Pagination.vue';
import Button from '@/components/ui/button/Button.vue';
import { useRelativeTime } from '@/composables/useFormatDate';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { Arrangement, Comment, PaginatedResponse, Tunebook, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Ban, Check, CircleDotDashed } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineOptions({ layout: PersistentLayout })

const props = defineProps<{
    arrangements: PaginatedResponse<Arrangement>;
    comments: PaginatedResponse<Comment>;
    tunebooks: PaginatedResponse<Tunebook>;
    otherComments: PaginatedResponse<Comment>;
}>();
console.log(props);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Dashboard'),
        href: '/dashboard',
    },
];

const onArrangementPageChange = (page: number) => {
    router.get(
        route('dashboard'),
        {
            arrangements: page,
            comments: props.comments.current_page,
            tunebooks: props.tunebooks.current_page,
            otherComments: props.otherComments.current_page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const onCommentPageChange = (page: number) => {
    router.get(
        route('dashboard'),
        {
            arrangements: props.arrangements.current_page,
            comments: page,
            tunebooks: props.tunebooks.current_page,
            otherComments: props.otherComments.current_page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const onTunebookPageChange = (page: number) => {
    router.get(
        route('dashboard'),
        {
            arrangements: props.arrangements.current_page,
            comments: props.comments.current_page,
            otherComments: props.otherComments.current_page,
            tunebooks: page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const onOtherCommentPageChange = (page: number) => {
    router.get(
        route('dashboard'),
        {
            arrangements: props.arrangements.current_page,
            comments: props.comments.current_page,
            tunebooks: props.tunebooks.current_page,
            otherComments: page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const approveComment = (commentId: number) => {
    router.post(route('comments.approve', { comment: commentId }), {}, {
        preserveScroll: true,
    });
};

const rejectComment = (commentId: number) => {
    router.post(route('comments.reject', { comment: commentId }), {}, {
        preserveScroll: true,
    });
};
const capitalizeFirstLetter = (string: string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>
<template>

    <Head :title="t('Dashboard')" />
    <AppLayout :breadcrumbs="breadcrumbs" noSidebar>
        <Heading>
            {{ t('Dashboard') }}
        </Heading>
        <div class="columns-1 lg:columns-2 gap-10 space-y-10">
            <div class="space-y-4 break-inside-avoid-column">
                <HeadingSmall v-if="arrangements.data.length">{{ t('My Arrangements') }}</HeadingSmall>
                <ul class="space-y-5" v-if="arrangements.data.length">
                    <ListItem v-for="arrangement in arrangements.data" :key="arrangement.id">
                        <Link :href="route('arrangements.show', { arrangement: arrangement.id })" class="space-y-3">
                        <p class="font-medium">{{ arrangement.tune.title }}</p>
                        <Description>
                            {{ useRelativeTime().relativeTime(arrangement.created_at) }}
                        </Description>
                        </Link>
                    </ListItem>
                </ul>
                <Pagination :data="arrangements" @pageChange="onArrangementPageChange" class="ml-0" />
            </div>


            <div class="space-y-4 break-inside-avoid-column">
                <HeadingSmall v-if="comments.data.length">{{ t('My Comments') }}</HeadingSmall>
                <ul class="space-y-5">
                    <ListItem v-for="comment in comments.data" :key="comment.id" class="space-y-3">

                        <div class="flex gap-2 items-baseline">
                            <p class="font-medium">
                                {{ comment.user.name }}
                            </p>
                            <Description>
                                {{ useRelativeTime().relativeTime(comment.created_at) }}
                            </Description>
                        </div>
                        <p>{{ comment.content }}</p>
                        <div class="flex items-center justify-between">
                            <Link :href="comment.commentable_type === 'App\\Models\\Arrangement' ?
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
                            <Description v-if="comment.is_suggestion"
                                class="flex items-center gap-1 justify-center rounded-full p-1">
                                {{ capitalizeFirstLetter(t((comment.status))) }}
                                <Check v-if="comment.status === 'approved'"
                                    class="inline-block text-green-500 size-4" />
                                <CircleDotDashed v-if="comment.status === 'pending'"
                                    class="inline-block text-yellow-500 size-4" />
                                <Ban v-if="comment.status === 'rejected'" class="inline-block text-red-500 size-4" />
                            </Description>
                        </div>

                    </ListItem>
                </ul>
                <Pagination :data="comments" @pageChange="onCommentPageChange" />
            </div>


            <div class="space-y-4 break-inside-avoid-column">
                <HeadingSmall v-if="tunebooks.data.length">{{ t('My Tunebooks') }}</HeadingSmall>
                <ul class="space-y-5" v-if="tunebooks.data.length">
                    <ListItem v-for="tunebook in tunebooks.data" :key="tunebook.id">
                        <Link :href="route('arrangements.index', { tunebook: tunebook.name })" class="space-y-2">
                        <p class="font-medium">{{ tunebook.name }}</p>
                        <Description>
                            {{ useRelativeTime().relativeTime(tunebook.created_at) }}
                        </Description>
                        </Link>
                    </ListItem>
                </ul>
                <Pagination :data="tunebooks" @pageChange="onTunebookPageChange" />
            </div>


            <div class="space-y-4 break-inside-avoid-column">
                <HeadingSmall v-if="otherComments.data.length">{{ t('Comments for me') }}</HeadingSmall>
                <ul class="space-y-5">
                    <ListItem v-for="comment in otherComments.data" :key="comment.id">
                        <div class="space-y-3">
                            <div class="flex gap-2 items-baseline">
                                <p class="font-medium">
                                    {{ comment.user.name }}
                                </p>
                                <Description>
                                    {{ useRelativeTime().relativeTime(comment.created_at) }}
                                </Description>
                            </div>
                            <p>{{ comment.content }}</p>
                            <div class="flex items-center justify-between">
                                <Link class="" :href="comment.commentable_type === 'App\\Models\\Arrangement' ?
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
                                <div class="flex flex-col items-end">
                                    <Description v-if="comment.is_suggestion"
                                        class="flex items-center gap-1 justify-center  rounded-full p-1">
                                        {{ capitalizeFirstLetter(t((comment.status))) }}
                                        <Check v-if="comment.status === 'approved'"
                                            class="inline-block text-green-500 size-4" />
                                        <CircleDotDashed v-if="comment.status === 'pending'"
                                            class="inline-block text-yellow-500 size-4" />
                                        <Ban v-if="comment.status === 'rejected'"
                                            class="inline-block text-red-500 size-4" />
                                    </Description>
                                    <Description v-if="comment.is_suggestion" class="flex items-center mt-1 gap-1">
                                        <Button v-if="comment.status != 'approved'" @click="approveComment(comment.id)"
                                            size="sm" variant="outline"
                                            class="text-sm text-muted-foreground cursor-pointer">
                                            <span>{{ t('Approve') }}</span>
                                        </Button>
                                        <Button v-if="comment.status != 'rejected'" @click="rejectComment(comment.id)"
                                            size="sm" variant="outline"
                                            class="text-sm text-muted-foreground cursor-pointer">
                                            <span>{{ t('Reject') }}</span>
                                        </Button>
                                    </Description>
                                </div>
                            </div>

                        </div>
                    </ListItem>
                </ul>
                <Pagination :data="otherComments" @pageChange="onOtherCommentPageChange" />
            </div>
        </div>
    </AppLayout>
</template>
