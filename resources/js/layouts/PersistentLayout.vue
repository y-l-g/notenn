<script setup lang="ts">
import Synth from '@/components/app/abcjs/Synth.vue';
import { useSyncLocale } from '@/composables/useSyncLanguage';
useSyncLocale().syncWithBackend()
</script>
<template>
    <div>
        <slot />
        <Synth />
    </div>
</template>
