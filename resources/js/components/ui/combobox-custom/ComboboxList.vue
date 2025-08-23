<script setup lang="ts">
import type { ComboboxContentEmits, ComboboxContentProps } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { reactiveOmit } from '@vueuse/core'
import { ComboboxContent, ComboboxPortal, useForwardPropsEmits } from 'reka-ui'

const props = withDefaults(defineProps<ComboboxContentProps & {
    class?: HTMLAttributes['class'],
    viewportClass?: HTMLAttributes['class'],
    width?: number,
}>(),
    {
        position: 'popper',
        align: 'center',
        sideOffset: 4,
    })

const widthStyle = computed(() => ({
    width: `${props.width ?? 200}px`,
}));
const emits = defineEmits<ComboboxContentEmits>()

const delegatedProps = reactiveOmit(props, 'class', 'viewportClass')
const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
    <ComboboxContent
        :style="widthStyle"
        data-slot="combobox-list"
        v-bind="forwarded"
        :class="cn('z-50 rounded-md border bg-popover text-popover-foreground origin-(--reka-combobox-content-transform-origin) overflow-hidden shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2', props.class)"
    >
        <slot />
    </ComboboxContent>
</template>
