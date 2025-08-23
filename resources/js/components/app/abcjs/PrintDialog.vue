<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from '@/components/ui/dialog'
import Label from '@/components/ui/label/Label.vue';
import { Arrangement } from '@/types';
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n';
import { useAbcNotation } from '@/composables/useAbcNotation';
const { t } = useI18n();
const props = defineProps<{
    modelValue: boolean
    arrangement: Arrangement
}>()

const emit = defineEmits(['update:modelValue'])

const printOptions = ref({
    showTitle: true,
    showAnnotations: true,
})

const abcjsParams = computed(() => ({
    format: {
        titlefont: printOptions.value.showTitle ? 20 : 0,
        annotationfont: printOptions.value.showAnnotations ? 14 : 0,
        composerfont: 0,
        subtitlefont: 0,
        transcriptionfont: 0,
        textfont: printOptions.value.showAnnotations ? 14 : 0,
        infofont: printOptions.value.showAnnotations ? 14 : 0,
        partsfont: 0,
        // wordsfont: printOptions.value.showW ? 14 : 0,
    },
    oneSvgPerLine: true,
    responsive: "resize",
    staffwidth: 700,
    paddingtop: 20,
    paddingbottom: 20,
    paddingleft: 20,
    paddingright: 20,
    wrap: {
        minSpacing: 1.8,
        maxSpacing: 2.8,
        preferredMeasuresPerLine: 4
    }
}));

const printScore = () => {
    const printWindow = window.open('', '_blank')
    if (!printWindow) return

    const abcContent = computed(() => (
        printOptions.value.showAnnotations ? useAbcNotation(props.arrangement).abcNotation : useAbcNotation(props.arrangement).abcNotationWithoutWordsAndNotesAndTranscription
    ));

    printWindow.document.write(`
<!DOCTYPE html>
<html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/abcjs@latest/dist/abcjs-basic.min.js"><\/script>
    <style>
      body { margin: 0; padding: 10mm; }
      @page { size: A4; margin: 0; }
    </style>
  </head>
  <body>
    <div id="printable-score"></div>
    <script>
      ABCJS.renderAbc(
        "printable-score",
        \`${abcContent.value.replace(/`/g, '\\`')}\`,
        ${JSON.stringify(abcjsParams.value).replace(/</g, '\\u003c')}
      );
      setTimeout(() => {
        window.print();
        window.close();
      }, 500);
    <\/script>
  </body>
</html>
`);

    printWindow.document.close()
    emit('update:modelValue', false)
}
</script>

<template>
    <Dialog :open="modelValue" @update:open="(val) => emit('update:modelValue', val)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('Print Settings') }}</DialogTitle>
            </DialogHeader>

            <div class="grid gap-4 py-4">
                <div class="flex items-center gap-2">
                    <Checkbox id="showTitle" v-model="printOptions.showTitle" />
                    <Label for="showTitle">{{ t('Show title') }}</Label>
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="showAnnotations" v-model="printOptions.showAnnotations" />
                    <Label for="showAnnotations">{{ t('Show Notes and words') }}</Label>
                </div>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="emit('update:modelValue', false)">
                    {{ t('Cancel') }}
                </Button>
                <Button @click="printScore">{{ t('Print') }}</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
