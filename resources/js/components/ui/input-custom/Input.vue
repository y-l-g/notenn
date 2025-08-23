<script setup lang="ts">
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core';
import { X } from 'lucide-vue-next'
import { useForwardPropsEmits } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'
import Button from '../button/Button.vue';

const props = defineProps<{
    defaultValue?: string | number
    modelValue?: string | number
    class?: HTMLAttributes['class']
    clearable?: boolean
    width?: number
}>();

const widthStyle = computed(() => ({
    width: `${props.width ?? 200}px`,
}));

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void
}>()


const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
})

const handleClear = () => {
    modelValue.value = undefined
}

defineOptions({
    inheritAttrs: false,
})

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props
    return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
    <div
        class="flex items-center"
        :style="widthStyle"
    >
        <div
            data-slot="input-wrapper"
            class="flex h-9 items-center gap-2 border-b px-3 w-full"
        >
            <input
                v-model="modelValue"
                data-slot="input"
                :class="cn(
                    'placeholder:text-muted-foreground flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50 truncate',
                    props.class,
                )"
                v-bind="{ ...forwarded, ...$attrs }"
            />
        </div>
        <Button
            variant="ghost"
            v-if="clearable && modelValue"
            @click="
                handleClear"
            class="size-5"
        >
            <X class="text-muted-foreground" />
        </Button>
    </div>
</template>
