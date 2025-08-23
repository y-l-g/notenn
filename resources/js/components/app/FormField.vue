<script setup lang="ts">
import { useWindowSize } from '@vueuse/core';
import InputError from '../laravel-starter-kit/InputError.vue';
import Description from './Description.vue';

defineProps<{
    description: string;
    error?: string;
    required?: boolean;
}>();

const { width } = useWindowSize()
</script>

<template>
    <div class="h-10">
        <div class="flex items-center">
            <Description class="min-w-7" v-if="width > 250">{{ description }}</Description>
            <slot />
        </div>
        <InputError v-if="error" :message="error" />
    </div>
</template>
