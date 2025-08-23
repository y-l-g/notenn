<!-- components/Tunebook/Arrangements/OtherArrangementsList.vue -->
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';
import { Arrangement, Tune, User } from '@/types';
import { useI18n } from 'vue-i18n';
import ListItem from '../ListItem.vue';
import HeadingSmall from '../laravel-starter-kit-customized/HeadingSmall.vue';
import Description from '../Description.vue';
const { t } = useI18n();
defineProps<{
    arrangements: Arrangement[];
    tune: Tune
    user?: User
}>();
</script>

<template>
    <ul class="space-y-4">
        <ListItem v-for="arrangement in arrangements" :key="arrangement.id">
            <Link :href="route('arrangements.show', { arrangement: arrangement.id })" class="flex justify-between"
                prefetch>
            <div>
                <HeadingSmall>{{ tune.title }}</HeadingSmall>
                <Description>{{ t('Added by') }} {{ arrangement.user?.name ?? t('Santa Klaus') }}</Description>
            </div>
            <span class="flex items-center gap-1 rounded-full px-2 py-1 text-sm">
                {{ arrangement.likes_count }}
                <Heart class="size-4" />
            </span>
            </Link>
        </ListItem>
    </ul>
</template>
