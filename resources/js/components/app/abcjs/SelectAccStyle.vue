<script setup lang="ts">
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import { usePlayerStore } from '@/Stores/player';
import { ChevronDown } from 'lucide-vue-next';
import { SelectRoot, SelectTrigger } from 'reka-ui';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const fourFour = computed(() => {
    return (playerStore.visualObjRef.getMeterFraction().den === 4 && playerStore.visualObjRef.getMeterFraction().num === 4);
})
const accStyles = computed(() => {
    if (fourFour.value) {
        return [
            { name: "Boom Chick", value: "bc" },
            { name: "jazz", value: "bzczbzcz" },
            { name: "arpeggio", value: "GHIJghij" },
            { name: "tango", value: "bzzcbzbz" },
        ];
    }
    return [];

})

const playerStore = usePlayerStore();

</script>

<template>
    <SelectRoot @update:modelValue="(value) => {
        playerStore.setMidiGchord(String(value));
    }">
        <SelectTrigger class="text-xs text-left border flex items-center gap-2 rounded p-0.5">
            {{ t('Backing Style') }}
            <ChevronDown class="size-3" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem
                v-for="style in accStyles"
                :key="style.name"
                :value="style.value"
            >
                {{ style.name }}
            </SelectItem>
        </SelectContent>
    </SelectRoot>
</template>
