<script setup lang="ts">
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import { usePlayerStore } from '@/Stores/player';
import { ChevronDown } from 'lucide-vue-next';
import { SelectTrigger } from 'reka-ui';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const bassInstruments = [
    { name: t("Acoustic Grand Piano"), value: "0" },
    { name: t("Accordion"), value: "21" },
    { name: t("Acoustic Guitar (Nylon)"), value: "24" },
    { name: t("Tenor Sax"), value: "66" },
    { name: t("Cello"), value: "42" },
    { name: t("Vibraphone"), value: "11" }
];

const playerStore = usePlayerStore();
</script>

<template>
    <Select @update:modelValue="(value) => {
        playerStore.setBassProgram(Number(value));
    }">
        <SelectTrigger class="text-xs text-left border flex items-center gap-2 rounded p-0.5">
            {{ t('Bass Instrument') }}
            <ChevronDown class="size-3" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectItem
                    v-for="instrument in bassInstruments"
                    :key="instrument.value"
                    :value="instrument.value"
                >
                    {{ instrument.name }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
