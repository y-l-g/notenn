<script setup lang="ts">
import { usePlayerStore } from '@/Stores/player';
import { getAbcjsParams } from '@/utils';
import { useWindowSize } from '@vueuse/core';
import { renderAbc } from 'abcjs';
import { onMounted, ref, watch } from 'vue';
const playerStore = usePlayerStore();

const props = defineProps<{
    abcString: string
    setTune?: boolean
}>();
const target = ref<HTMLElement | null>(null);
const { width } = useWindowSize()
const tune = ref()
defineExpose({
    tune,
    target,
})
onMounted(() => {
    if (!target.value) return;
    tune.value = renderAbc(target.value, props.abcString, getAbcjsParams(width.value))[0]
    if (props.setTune) {
        playerStore.setTune(tune.value);
        playerStore.targetElement = target.value
    }
    watch(width, (newVal, oldVal) => {
        if (!target.value) return;
        const newSize = newVal < 500 ? 'small' : 'large';
        const oldSize = oldVal < 500 ? 'small' : 'large';
        if (newSize !== oldSize) {
            const tune = renderAbc(target.value, props.abcString, getAbcjsParams(width.value))[0]
            if (props.setTune) playerStore.setTune(tune);
        }
    });
});

</script>

<template>
    <div class="mx-[-5px] xl:mx-0 ">
        <div ref="target"></div>
    </div>
</template>
