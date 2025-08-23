<script setup lang="ts">
import { Arrangement } from '@/types';
import Accordion from '../ui/accordion/Accordion.vue';
import AccordionContent from '../ui/accordion/AccordionContent.vue';
import AccordionItem from '../ui/accordion/AccordionItem.vue';
import AccordionTrigger from '../ui/accordion/AccordionTrigger.vue';
import Description from './Description.vue';
import HeadingSmall from './laravel-starter-kit-customized/HeadingSmall.vue';
import { useAbcNotation } from '@/composables/useAbcNotation';
import { useI18n } from 'vue-i18n';
const { t } = useI18n()
const props = defineProps<{
    arrangement: Arrangement;
    type: "Notes" | "ABC Notation" | "Words"
}>();

const notation = useAbcNotation(props.arrangement);
const content =
    props.type === "Notes" ? notation.notes :
        props.type === "ABC Notation" ? notation.abcNotation :
            notation.words;
</script>

<template>
    <Accordion v-if="content" type="single" collapsible class=" w-full" default-value="[]">
        <AccordionItem value="notes">
            <AccordionTrigger class="py-1  justify-normal cursor-pointer">
                <HeadingSmall class="cursor-pointer">
                    {{ t(type) }}
                </HeadingSmall>
            </AccordionTrigger>
            <AccordionContent>
                <Description>
                    <pre class="overflow-auto whitespace-pre break-all text-sm">{{ content }}</pre>
                </Description>
            </AccordionContent>
        </AccordionItem>
    </Accordion>
</template>
