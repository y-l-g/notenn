<script setup lang="ts">
import type { Comment } from '@/types';
import ListItem from '../ListItem.vue';
import Description from '../Description.vue';
import { useRelativeTime } from '@/composables/useFormatDate';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Ban, Check, CircleDotDashed, Star, User2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
const { t } = useI18n();
defineProps<{
    comment: Comment;
}>();

const page = usePage();

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
    <ListItem>
        <div class="space-y-3">
            <div class="flex gap-2 items-baseline justify-between">
                <div class="flex items-center gap-2">
                    <User2 class="size-4" />
                    <span class="font-medium">
                        {{ comment.user.name }}
                    </span>
                    <Description>
                        {{ useRelativeTime().relativeTime(comment.created_at) }}
                    </Description>
                </div>
                <div v-if="comment.is_suggestion" class="flex items-center gap-1">
                    <Star class="text-yellow-500 size-4" /><span class="text-xs ">Suggestion</span>
                </div>
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
                        <Check v-if="comment.status === 'approved'" class="inline-block text-green-500 size-4" />
                        <CircleDotDashed v-if="comment.status === 'pending'"
                            class="inline-block text-yellow-500 size-4" />
                        <Ban v-if="comment.status === 'rejected'" class="inline-block text-red-500 size-4" />
                    </Description>
                    <Description
                        v-if="comment.is_suggestion && comment.commentable.user_id === page.props.auth?.user?.id"
                        class="flex items-center mt-1 gap-1">
                        <Button v-if="comment.status != 'approved'" @click="approveComment(comment.id)" size="sm"
                            variant="outline" class="text-sm text-muted-foreground cursor-pointer">
                            <span>{{ t('Approve') }}</span>
                        </Button>
                        <Button v-if="comment.status != 'rejected'" @click="rejectComment(comment.id)" size="sm"
                            variant="outline" class="text-sm text-muted-foreground cursor-pointer">
                            <span>{{ t('Reject') }}</span>
                        </Button>
                    </Description>
                </div>
            </div>

        </div>
    </ListItem>
</template>
