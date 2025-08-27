<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BlogPost, BreadcrumbItem } from '@/types';
import { useI18n } from 'vue-i18n';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import Description from '@/components/app/Description.vue';
import { useRelativeTime } from '@/composables/useFormatDate';
import { ArrowLeft } from 'lucide-vue-next';
import { marked } from 'marked';

const { t } = useI18n();


const props = defineProps<{
    post: BlogPost
}>()

const page = usePage()
if (props.post.meta_title) {
    page.props.title = props.post.meta_title
}
if (props.post.meta_description) {
    page.props.description = props.post.meta_description
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: '/blog',
    },
];
</script>

<template>

    <Head :title="t('Blog')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="blog-post">
            <Heading>
                {{ post.title }}
            </Heading>
            <Description>
                {{ useRelativeTime().relativeTime(post.published_at) }}
            </Description>

            <div class="content markdown-body" v-html="marked.parse(post.content)" />
            <Link :href="route('blog.index')" class="flex gap-2 items-center" prefetch>
            <ArrowLeft class="size-4" /> <span>{{ t('Return to Blog') }}</span>
            </Link>
        </div>
    </AppLayout>
</template>

<style scoped>
.blog-post :deep(.markdown-body) {
    --heading-color: var(--foreground);
    --text-color: var(--foreground);
    --code-bg: var(--muted);
    --border-color: var(--border);
    --link-color: var(--primary);
    --quote-border: var(--border);
    --quote-text: var(--muted-foreground);

    line-height: 1.6;
    color: var(--text-color);

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin: 1.5rem 0 1rem;
        font-weight: 600;
        color: var(--heading-color);
    }

    h1 {
        font-size: 1.5rem;
    }

    h2 {
        font-size: 1.25rem;
    }

    h3 {
        font-size: 1.125rem;
    }

    p {
        margin: 0 0 1rem;
    }

    strong {
        font-weight: 600;
    }

    em {
        font-style: italic;
    }

    hr {
        border: 0;
        border-top: 1px solid var(--border-color);
        margin: 1.5rem 0;
    }

    ul,
    ol {
        margin: 0 0 1rem 1.25rem;
        padding: 0;
    }

    li {
        margin-bottom: 0.25rem;
        list-style-type: disc
    }

    pre,
    code {
        font-family: monospace;
        background: var(--code-bg);
        border-radius: var(--radius);
    }

    pre {
        padding: 0.75rem;
        overflow-x: auto;
        margin: 0 0 1rem;
    }

    code {
        padding: 0.2rem 0.3rem;
    }

    blockquote {
        border-left: 3px solid var(--quote-border);
        padding-left: 0.75rem;
        margin: 0 0 1rem;
        color: var(--quote-text);
    }

    a {
        color: var(--link-color);
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    img {
        max-width: 100%;
        height: auto;
        margin: 0 0 1rem;
    }

    table {
        border-collapse: collapse;
        margin: 0 0 1rem;
        width: 100%;
    }

    th,
    td {
        padding: 0.5rem;
        border: 1px solid var(--border-color);
    }

    th {
        background-color: var(--code-bg);
    }
}
</style>
