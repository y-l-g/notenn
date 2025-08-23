<script setup lang="ts">
import {
    Combobox,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxItem,
    ComboboxItemIndicator,
} from '@/components/ui/combobox';
import { Check, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import ComboboxInput from '../ui/combobox-custom/ComboboxInput.vue';
import Button from '../ui/button/Button.vue';
import ComboboxList from '../ui/combobox-custom/ComboboxList.vue';
import ComboboxAnchor from '../ui/combobox-custom/ComboboxAnchor.vue';

import { useFocusWithin } from '@vueuse/core'
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const target = ref()
const { focused } = useFocusWithin(target)

const props = defineProps<{
    options: Array<{ label: string; value: string }>;
    placeholder?: string;
    selectedValue?: string | undefined;
    width?: number
}>();

const widthStyle = computed(() => ({
    width: `${props.width ?? 200}px`,
}));

const emit = defineEmits(['update:selectedValue']);

const inputValue = ref('');
const isOpen = ref(false);

watch(() => props.selectedValue, (newVal) => {
    const option = props.options.find(opt => opt.value === newVal);
    inputValue.value = option?.label || '';
}, { immediate: true });

const handleClear = () => {
    emit('update:selectedValue', undefined);
    inputValue.value = '';
    isOpen.value = false;
};

const handleSelect = (value: string) => {
    emit('update:selectedValue', value);
    isOpen.value = false;
};
const handleInputBlur = () => {
    setTimeout(() => {
        if (props.selectedValue) {
            const selectedOption = props.options.find(opt => opt.value === props.selectedValue);
            inputValue.value = selectedOption?.label || '';
        } else {
            inputValue.value = '';
        }
        isOpen.value = false;
    }, 200);
};

</script>

<template>
    <Combobox v-model="inputValue" :style="widthStyle" :open="isOpen" @update:open="isOpen = $event">
        <ComboboxAnchor :style="widthStyle" class="flex items-center">
            <ComboboxInput :width @blur="handleInputBlur" :model-value="inputValue" :placeholder="placeholder"
                class="pr-4 text-sm" ref="target" @update:model-value="(val) => {
                    inputValue = val;
                    if (!val) emit('update:selectedValue', undefined);
                }" @click="isOpen = focused ?? true" />

            <Button variant="ghost" v-if="inputValue" @click="handleClear" class="size-5">
                <X class="text-muted-foreground" />
            </Button>
        </ComboboxAnchor>
        <ComboboxList :width>
            <ComboboxEmpty>{{ t('No results') }}</ComboboxEmpty>
            <ComboboxGroup class="max-h-[300px] overflow-y-scroll">
                <ComboboxItem v-for="option in options" :key="option.value" :value="option.label" class="px-2 py-1.5"
                    @select="() => handleSelect(option.value)">
                    <span class="truncate">{{ option.label }}</span>
                    <ComboboxItemIndicator>
                        <Check />
                    </ComboboxItemIndicator>
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
</template>
