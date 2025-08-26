<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3';
import { useWindowSize } from '@vueuse/core';
import { computed, HTMLAttributes, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { KEYS } from '@/constants';
import FormField from '../FormField.vue';
import Input from '@/components/ui/input-custom/Input.vue';
import Combobox from '../Combobox.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import InputError from '@/components/laravel-starter-kit/InputError.vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    rhythms: Array<{ id: number; name: string }>,
    meters: Array<{ id: number; name: string }>
    class?: HTMLAttributes['class']
}>();

const { t } = useI18n();
const form = defineModel<InertiaForm<any>>('form')
const { width } = useWindowSize()
const inputWidth = computed(() => {
    return width.value < 400 ? 200 : 270;
});

const selectedRhythm = ref(form.value.rhythm_id?.toString());
const selectedMeter = ref(form.value.meter_id?.toString());

const mapOptions = (items: Array<{ id: number, name: string }> | string[]) => items.map(item => ({
    value: typeof item === 'string' ? item : item.id.toString(),
    label: typeof item === 'string' ? item : item.name
}));

const rhythmOptions = computed(() => mapOptions(props.rhythms));
const meterOptions = computed(() => mapOptions(props.meters));
const keyOptions = computed(() => mapOptions(KEYS));

watch(selectedRhythm, (newValue) => {
    form.value.rhythm_id = newValue ? parseInt(newValue) : null;
});

watch(selectedMeter, (newValue) => {
    form.value.meter_id = newValue ? parseInt(newValue) : null;
});

</script>

<template>
    <div :class="cn('space-y-3', props.class)">
        <FormField description="*" :error="form.errors.name">
            <Input v-model="form.name" :placeholder="t('Arrangement Name') + ' ' + t('(optional)')" :width="inputWidth"
                clearable />
        </FormField>
        <FormField description="R" :error="form.errors.rhythm_id">
            <Combobox v-model:selectedValue="selectedRhythm" :options="rhythmOptions"
                :placeholder="t('Rhythm / Dance') + ' ' + t('(optional)')" :width="inputWidth" />
        </FormField>

        <FormField description="M" :error="form.errors.meter_id">
            <Combobox v-model:selectedValue="selectedMeter" :options="meterOptions"
                :placeholder="t('Meter (4/4 , 3/4...)') + ' ' + t('(optional)')" :width="inputWidth" />
        </FormField>
        <FormField description="P" :error="form.errors.parts">
            <Input v-model="form.parts" :placeholder="t('Parts (e.g. AABB)') + ' ' + t('(optional)')"
                :width="inputWidth" clearable />
        </FormField>
        <FormField description="Q" :error="form.errors.tempo">
            <Input type="number" v-model="form.tempo" :placeholder="t('Tempo') + ' ' + t('(required)')"
                :width="inputWidth" clearable required />
        </FormField>

        <FormField class="h-full" description="N" :error="form.errors.notes">
            <Textarea class="text-sm w-[200px] sm:w-[280px] mr-1" v-model="form.notes"
                :placeholder="t('Notes') + ' ' + t('(optional)')" />
        </FormField>
        <FormField class="h-full" description="W" :error="form.errors.words">
            <Textarea class="text-sm w-[200px] sm:w-[280px] mr-1" v-model="form.words"
                :placeholder="t('Lyrics') + ' ' + t('(optional)')" />
        </FormField>
        <FormField description="Z" :error="form.errors.transcription">
            <Input v-model="form.transcription" :placeholder="t('Transcription') + ' ' + t('(optional)')"
                :width="inputWidth" clearable />
        </FormField>
        <FormField class="h-full" description="K" :error="form.errors.key">
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-5">
                <Combobox v-model:selectedValue="form.key" :options="keyOptions" :placeholder="t('Key')"
                    :width="inputWidth" />
            </div>
            <InputError v-if="form.errors.key" :message="form.errors.key" />
        </FormField>
    </div>
</template>
