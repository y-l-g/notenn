<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3';
import abcjs from 'abcjs';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { useWindowSize } from '@vueuse/core';
import { usePlayerStore } from '@/Stores/player';
import { useI18n } from 'vue-i18n';
import { getAbcjsParams } from '@/utils';
import Description from '../Description.vue';
import InputError from '@/components/laravel-starter-kit/InputError.vue';
import TunePlayer from '../TunePlayer.vue';
const props = defineProps<{
    composers: Array<{ id: number; name: string }>,
    rhythms: Array<{ id: number; name: string }>,
    origins: Array<{ id: number; name: string }>,
    meters: Array<{ id: number; name: string }>,
}>();

const paper = ref<HTMLDivElement | undefined>(undefined);

const { width } = useWindowSize()
const PlayerStore = usePlayerStore();
const { t } = useI18n();

const form = defineModel<InertiaForm<any>>('form');
const editableTuneBody = ref(form.value.tune_body);
watch(editableTuneBody, (newValue) => form.value.tune_body = newValue);

const abcHeader = computed(() => {
    const headerLines = [
        'X: 1',
        `T: ${form.value.title || 'Title'}`,
        form.value.composer_id && `C: ${props.composers.find(c => c.id === form.value.composer_id)?.name}`,
        form.value.origin_id && `O: ${props.origins.find(o => o.id === form.value.origin_id)?.name}`,
        form.value.meter_id && `M: ${props.meters.find(m => m.id === form.value.meter_id)?.name}`,
        form.value.tempo && `Q: ${form.value.tempo}`,
        form.value.parts && `P: ${form.value.parts}`,
        form.value.transcription && `Z: ${form.value.transcription}`,
        form.value.rhythm_id && `R: ${props.rhythms.find(r => r.id === form.value.rhythm_id)?.name}`,
        form.value.key && `K: ${[form.value.key, form.value.mode].filter(Boolean).join(' ') || 'C'}`
    ];
    return headerLines.filter(Boolean).join('\n');
});

const completeAbcNotation = computed({
    get: () => `${abcHeader.value}\n${editableTuneBody.value}`,
    set: (newValue) => {
        const headerLines = abcHeader.value.split('\n');
        const headerEndIndex = newValue.split('\n').findIndex(line => !headerLines.includes(line));
        if (headerEndIndex !== -1) editableTuneBody.value = newValue.split('\n').slice(headerEndIndex).join('\n');
    }
});

const handleTextareaInteraction = (e: Event | MouseEvent) => {
    const textarea = e.target as HTMLTextAreaElement;
    const headerLength = abcHeader.value.length + 1;
    const cursorInHeader = textarea.selectionStart < headerLength;
    if (cursorInHeader) {
        if (e.type === 'input') {
            textarea.value = completeAbcNotation.value;
            nextTick(() => textarea.setSelectionRange(headerLength, headerLength));
        } else {
            textarea.setSelectionRange(headerLength, headerLength);
        }
    } else if (e.type === 'input') {
        editableTuneBody.value = textarea.value.slice(headerLength);
    }
};

const editor = ref<any>(null);
const setupEditor = () => {
    editor.value = new abcjs.Editor("abcDisplayRef", {
        canvas_id: paper.value,
        warnings_id: 'warnings',
        abcjsParams: getAbcjsParams(width.value)
    });
};

onMounted(() => {
    setupEditor();
    PlayerStore.targetElement = paper.value || null;
    PlayerStore.setTune(editor.value.tunes[0]);
    watch(width, (newVal, oldVal) => {
        const newSize = newVal < 500 ? 'small' : 'large';
        const oldSize = oldVal < 500 ? 'small' : 'large';
        if (newSize !== oldSize) {
            setupEditor();
        }
    });
    watch(completeAbcNotation, (newVal) => {
        const textarea = document.getElementById('abcDisplayRef') as HTMLTextAreaElement;
        if (!textarea) return;
        textarea.value = newVal;
        editor.value.fireChanged();
        setupEditor()
        PlayerStore.synthControl.visualObj = editor.value.tunes[0];
        PlayerStore.setTune(editor.value.tunes[0]);
    }, { immediate: true });
});

</script>

<template>
    <Textarea class="max-w-[350px] xl:max-w-lg text-sm" v-model="completeAbcNotation" @input="handleTextareaInteraction"
        @click="handleTextareaInteraction" placeholder="Enter your ABC notation here" id="abcDisplayRef" />
    <Description>
        {{ t("Header can't be modified. W and N fields are hidden for convenience") }}
    </Description>
    <InputError v-if="form.errors.tune_body" :message="form.errors.tune_body" />
    <div v-show="editableTuneBody?.length > 0" class="mx-[-5px] xl:mx-0 space-y-5">
        <div ref="paper"></div>
        <TunePlayer v-show="editableTuneBody?.length > 0" type="button" :tune-obj="PlayerStore.synthControl.visualObj"
            :abc="completeAbcNotation" isCurrent />
    </div>


</template>
