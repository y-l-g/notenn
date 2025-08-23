<script setup lang="ts">
defineProps<{
    content: string;
}>();
</script>

<template>
    <pre class="overflow-x-auto rounded-md bg-secondary p-4 font-mono text-sm">{{ content }}</pre>
</template>
