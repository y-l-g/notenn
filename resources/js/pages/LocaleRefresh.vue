<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
const props = defineProps<{
    target: string
}>();
router.visit(props.target);
</script>
<template>
    <AppLayout />
</template>
