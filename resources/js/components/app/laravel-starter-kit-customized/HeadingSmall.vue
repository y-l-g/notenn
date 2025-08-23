<template>
    <header>
        <h3 class="mb-1 font-medium text-lg">
            <slot />
        </h3>
        <p v-if="$slots.description" class="text-sm text-muted-foreground">
            <slot name="description" />
        </p>
    </header>
</template>
