<script setup lang="ts">
import Pagination from '@/components/app/Pagination.vue'
import { BlogPost, BreadcrumbItem, PaginatedResponse } from '@/types'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useI18n } from 'vue-i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import ListItem from '@/components/app/ListItem.vue';
import { useRelativeTime } from '@/composables/useFormatDate';
import Description from '@/components/app/Description.vue';
const { t } = useI18n();

const props = defineProps<{
    posts: PaginatedResponse<BlogPost>
    search?: string;
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: '/blog',
    },
];

const onPageChange = (page: number) => {
    router.get(
        props.posts.path,
        {
            page: page,
            search: props.search,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const getExcerpt = (content: string, length: number = 150) => {
    if (!content) return '';
    const strippedContent = content.replace(/(<([^>]+)>)/gi, "");
    if (strippedContent.length <= length) {
        return strippedContent;
    }
    return strippedContent.substring(0, length) + '...';
};

</script>
<template>

    <Head :title="t('Blog')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>Blog</Heading>
        <ul class="space-y-5">
            <ListItem v-for="post in posts.data" :key="post.id" class="space-y-3">
                <div>
                    <Link prefetch :href="route('blog.show', post.slug)" class="font-medium">
                    {{ post.title }}
                    </Link>
                </div>
                <Description>
                    <p class="mb-2">{{ getExcerpt(post.content) }}</p>
                    <Link :href="route('blog.show', post.slug)" class="flex gap-2 items-center" prefetch>
                    <span>{{ t('Lire la suite') }}</span>
                    </Link>
                </Description>
                <Description>
                    {{ useRelativeTime().relativeTime(post.published_at) }}

                </Description>
            </ListItem>
        </ul>
        <Pagination :data="posts" @pageChange="onPageChange" />
    </AppLayout>
</template>
