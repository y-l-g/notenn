<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import { usePlayerStore } from '@/Stores/player';
import { Piano } from 'lucide-vue-next';
import { SelectTrigger } from 'reka-ui';

const melodicInstruments = [
    { name: "Acoustic Grand Piano", value: "0" },
    { name: "Accordion", value: "21" },
    { name: "Violin", value: "40" },
    { name: "Flute", value: "73" },
    { name: "Acoustic Guitar (Nylon)", value: "24" },
    { name: "Trumpet", value: "56" },
    { name: "Tenor Sax", value: "66" },
    { name: "Cello", value: "42" },
    { name: "Clarinet", value: "71" },
    { name: "Harp", value: "46" },
    { name: "Vibraphone", value: "11" }
];


const playerStore = usePlayerStore();
</script>

<template>
    <Select @update:modelValue="(value) => {
        playerStore.setProgram(Number(value));
    }">
        <SelectTrigger class="">

            <Button variant="ghost" class="rounded-full">
                <Piano />
            </Button>

        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectItem v-for="instrument in melodicInstruments" :key="instrument.value" :value="instrument.value">
                    {{ instrument.name }}
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
