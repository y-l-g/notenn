<script setup lang="ts">
import { useAttrs, type HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'
import { SearchIcon } from 'lucide-vue-next';

const props = defineProps<{
    defaultValue?: string | number
    modelValue?: string | number
    class?: HTMLAttributes['class']
}>()

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
})

const attrs = useAttrs()

</script>

<template>
    <div class="flex h-9 items-center gap-2 border-b px-3 sm:w-[640px]">
        <SearchIcon class="size-4 shrink-0 opacity-50" />
        <input
            v-model="modelValue"
            v-bind="attrs"
            data-slot="input"
            :class="cn(
                'placeholder:text-muted-foreground flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50',
                'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                props.class,
            )"
        >
    </div>
</template>
