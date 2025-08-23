<template>
    <div class="mb-4 space-y-0.5">
        <h2 class="text-2xl font-medium tracking-tight">
            <slot />
        </h2>
        <p v-if="$slots.description" class="text-sm text-muted-foreground">
            <slot name="description" />
        </p>
    </div>
</template>
