<script setup lang="ts">
import HeadingSmall from '../laravel-starter-kit-customized/HeadingSmall.vue';
import Render from './Render.vue';

defineProps<{
    title?: string;
    abcString: string
}>();

</script>

<template>
    <div class="space-y-4 bg-muted/50 p-2 sm:p-5">
        <HeadingSmall>{{ title }}</HeadingSmall>
        <p class="rounded-md font-mono text-sm">{{ abcString }}</p>
        <Render :abc-string="abcString" />
    </div>
</template>
