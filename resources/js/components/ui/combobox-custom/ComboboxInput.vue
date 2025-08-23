<script setup lang="ts">
import { cn } from '@/lib/utils'
import { ComboboxInput, type ComboboxInputEmits, type ComboboxInputProps, useForwardPropsEmits } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'

defineOptions({
    inheritAttrs: false,
})

const props = defineProps<ComboboxInputProps & {
    class?: HTMLAttributes['class']
    width?: number
}>()

const widthStyle = computed(() => ({
    width: `${props.width ?? 200}px`,
}));

const emits = defineEmits<ComboboxInputEmits>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props

    return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
    <div
        :style="widthStyle"
        data-slot="command-input-wrapper"
        class="flex h-9 items-center gap-2 border-b px-3"
    >
        <ComboboxInput
            data-slot="command-input"
            :class="cn(
                'max-w-full placeholder:text-muted-foreground flex h-10  rounded-md bg-transparent py-3 text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50',
                props.class,
            )"
            v-bind="{ ...forwarded, ...$attrs }"
        >
            <slot />
        </ComboboxInput>
    </div>
</template>
