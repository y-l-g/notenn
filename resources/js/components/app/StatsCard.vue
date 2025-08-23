<script setup lang="ts">
import Description from '@/components/app/Description.vue';
import { Heart } from 'lucide-vue-next';
import { useRelativeTime } from '@/composables/useFormatDate';
import { Link } from '@inertiajs/vue3';
import HeadingSmall from './laravel-starter-kit-customized/HeadingSmall.vue';
import ListItem from './ListItem.vue';

defineProps<{
    title: string;
    items: {
        id: string | number;
        title: string;
        likes_count?: number;
        created_at: string;
        link: string
    }[];
}>();
</script>

<template>
    <div v-if="items.length" class="break-inside-avoid-column">
        <HeadingSmall>{{ title }}</HeadingSmall>
        <div class="space-y-4">
            <ListItem v-for="item in items" :key="item.id">
                <Link :href="item.link">
                <p class="font-medium">{{ item.title }}</p>
                <div class="flex items-center justify-between mt-2">
                    <Description class="flex gap-1 items-center">
                        {{ item.likes_count }}
                        <Heart class="size-3" />
                    </Description>
                    <Description>{{ useRelativeTime().relativeTime(item.created_at) }}</Description>
                </div>
                </Link>
            </ListItem>


        </div>
    </div>
</template>
