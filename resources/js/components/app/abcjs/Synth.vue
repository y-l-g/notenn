<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Slider from '@/components/ui/slider/Slider.vue';
import Toggle from '@/components/ui/toggle/Toggle.vue';
import { usePlayerStore } from '@/Stores/player';
import { ChevronDown, ChevronUp, Forward, Minus, Pause, Play, Plus, Repeat, TextCursorInput, X } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import SelectInstrument from './SelectInstrument.vue';
import SelectAccInstrument from './SelectAccInstrument.vue';
import SelectBassInstrument from './SelectBassInstrument.vue';
import SelectAccStyle from './SelectAccStyle.vue';
import { useI18n } from 'vue-i18n';
import { useColorMode } from '@vueuse/core';
import { usePage } from '@inertiajs/vue3';
import { User } from '@/types';
const page = usePage();
const user = page.props.auth.user as User;
const { t } = useI18n();
const playerStore = usePlayerStore();
const audio = ref<HTMLElement | null>(null);
const warp = ref(1);
const isCollapsed = ref(false);
const isExpanded = ref(false);

let isSettingWarp = false;

watch(warp, async (value) => {
    if (isSettingWarp) return;
    isSettingWarp = true;
    await playerStore.synthControl.setWarp(value * 100);
    isSettingWarp = false;
});

onMounted(() => {
    if (!audio.value) return;
    playerStore.synthControl.load(audio.value, playerStore.cursorControl, {
        displayLoop: true,
        displayRestart: true,
        displayPlay: true,
        displayProgress: true,
        displayWarp: true,
        displayClock: true,
    });
});

let wasPlaying = false;

const onSliderStart = () => {
    wasPlaying = playerStore.isPlaying || false;
    if (wasPlaying) playerStore.play();
    playerStore.isPlaying = false
};

const onSliderEnd = () => {
    if (wasPlaying) playerStore.play();
};
const colorMode = ref(useColorMode().store)
watch(useColorMode().store, (newval) => {
    colorMode.value = newval
})
</script>

<template>
    <div v-show="playerStore.displayPlayer"
        class="fixed bottom-20 left-[50vw] z-50 -translate-x-1/2 transform overflow-hidden rounded-lg border border-primary-muted bg-background/85 p-3 px-1 sm:px-3 pb-2 pt-8 backdrop-blur-sm transition-all duration-200 ease-in-out"
        :class="isCollapsed ? 'w-auto' : 'w-[90%] max-w-md'">
        <Button class="absolute right-2 top-1 rounded-full p-1 size-6 cursor-pointer" variant="ghost"
            @click="isCollapsed = !isCollapsed">
            <div class="relative h-4 w-4">
                <ChevronDown class="absolute inset-0 h-4 w-4 transition-all duration-200 ease-in-out"
                    :class="isCollapsed ? 'rotate-90 opacity-0' : 'rotate-0 opacity-100'" />
                <ChevronUp class="absolute inset-0 h-4 w-4 transition-all duration-200 ease-in-out"
                    :class="isCollapsed ? 'rotate-0 opacity-100' : '-rotate-90 opacity-0'" />
            </div>
        </Button>
        <Button class="absolute right-6 top-1 rounded-full p-1 size-6 mr-2 cursor-pointer" variant="ghost"
            @click="playerStore.closeSynth">
            <div class="relative h-4 w-4">
                <X class="absolute inset-0 h-4 w-4 transition-all duration-200 ease-in-out" />
            </div>
        </Button>

        <div v-if="!isCollapsed" class="flex flex-col gap-2">
            <Slider :min="0" :max="1" :step="0.01" @pointerdown="onSliderStart" @pointerup="onSliderEnd"
                v-model="playerStore.percentArray" class="w-full" />
            <div class="flex items-center gap-1 sm:gap-2 justify-between">
                <div class="flex-col gap-2 sm:flex-row ">
                    <Button variant="ghost" class="size-6 sm:size-8 rounded-full cursor-pointer"
                        @click="playerStore.toggleLoop()">
                        <Repeat v-if="playerStore.isLooping" />
                        <Forward v-else />
                    </Button>
                    <Button variant="ghost" class="size-6 sm:size-8 rounded-full cursor-pointer"
                        @click="playerStore.play()">
                        <Pause v-if="playerStore.isPlaying" />
                        <Play v-else />
                    </Button>
                </div>
                <div class="text-center min-w-0">
                    <div class="text-xs">{{ playerStore.clock }}</div>
                    <div class="text-xs font-medium" :class="['line-clamp-2 max-w-[150px] lg:max-w-[200px]']">{{
                        playerStore.visualObjRef.metaText.title }}</div>
                </div>
                <div class="flex items-center gap-1">
                    <div class="flex flex-col sm:flex-row items-center gap-1">
                        <Button variant="ghost" class="size-6 rounded-full cursor-pointer"
                            @click="warp = Math.max(0.4, warp - 0.1)">
                            <Minus class="h-3 w-3" />
                        </Button>
                        <span class="w-6 text-center text-xs">x{{ warp.toFixed(1) }}</span>
                        <Button variant="ghost" class="size-6 rounded-full cursor-pointer"
                            @click="warp = Math.min(2, warp + 0.1)">
                            <Plus class="h-3 w-3" />
                        </Button>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center">
                        <Toggle :model-value="playerStore.metronome" @update:model-value="playerStore.toggleMetronome"
                            size="sm" class="rounded-full relative">
                            <svg v-if="colorMode == 'dark'" width="800" height="800" viewBox="0 0 56 56"
                                xmlns="http://www.w3.org/2000/svg" fill="white">
                                <path
                                    d="M12.578 28.105 6.133 40.738c-.61 1.196-.914 2.461-.914 3.657 0 3.398 2.46 6.304 6.562 6.304H44.22c4.125 0 6.562-2.906 6.562-6.304 0-1.196-.304-2.461-.914-3.68L33.602 8.793C32.5 6.66 30.203 5.301 28 5.301c-2.18 0-4.5 1.36-5.578 3.492l-5.625 11.062 2.742 2.743 6.117-12.141c.492-1.008 1.36-1.664 2.344-1.664s1.852.656 2.367 1.664l12.797 25.5H29.242L9.226 15.941c-.773-.773-1.828-.796-2.648 0-.82.82-.773 1.899 0 2.696l17.344 17.32H12.836l2.555-5.04Zm17.39 4.899v-16.43c0-1.172-.702-1.922-1.874-1.922-1.149 0-1.852.75-1.852 1.922v12.703ZM11.806 47.02c-1.735 0-2.742-1.313-2.742-2.766 0-.492.117-1.008.351-1.523l1.594-3.141 33.984-.047 1.594 3.188c.258.492.398 1.007.398 1.5 0 1.476-1.03 2.788-2.765 2.788Z" />
                            </svg>
                            <svg v-else width="800" height="800" viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg"
                                fill="black">
                                <path
                                    d="M12.578 28.105 6.133 40.738c-.61 1.196-.914 2.461-.914 3.657 0 3.398 2.46 6.304 6.562 6.304H44.22c4.125 0 6.562-2.906 6.562-6.304 0-1.196-.304-2.461-.914-3.68L33.602 8.793C32.5 6.66 30.203 5.301 28 5.301c-2.18 0-4.5 1.36-5.578 3.492l-5.625 11.062 2.742 2.743 6.117-12.141c.492-1.008 1.36-1.664 2.344-1.664s1.852.656 2.367 1.664l12.797 25.5H29.242L9.226 15.941c-.773-.773-1.828-.796-2.648 0-.82.82-.773 1.899 0 2.696l17.344 17.32H12.836l2.555-5.04Zm17.39 4.899v-16.43c0-1.172-.702-1.922-1.874-1.922-1.149 0-1.852.75-1.852 1.922v12.703ZM11.806 47.02c-1.735 0-2.742-1.313-2.742-2.766 0-.492.117-1.008.351-1.523l1.594-3.141 33.984-.047 1.594 3.188c.258.492.398 1.007.398 1.5 0 1.476-1.03 2.788-2.765 2.788Z" />
                            </svg>

                        </Toggle>

                        <Toggle :model-value="playerStore.displayCursor" @update:model-value="playerStore.toggleCursor"
                            size="sm" class="rounded-full relative">
                            <TextCursorInput class="h-3 w-3" />

                        </Toggle>
                        <SelectInstrument v-if="user?.is_pro" />
                    </div>
                </div>
            </div>
            <div v-if="isExpanded" class="grid  sm:grid-cols-3 gap-1 p-1 grid-cols-2">
                <SelectAccInstrument />
                <SelectBassInstrument />
                <SelectAccStyle />
                <div @click="playerStore.toggleSwing()"
                    class="cursor-default flex items-center text-xs text-primary-muted border rounded p-0.5">
                    <span v-if="playerStore.swing">{{ t('Swing On') }}</span><span v-else>{{ t('Swing Off') }}</span>
                </div>
                <div @click="playerStore.toggleVoicesOff()"
                    class="cursor-default flex items-center text-xs text-primary-muted border rounded p-0.5">
                    <span v-if="!playerStore.voicesOff">{{ t('Mute Voices') }}</span><span v-else>{{ t('Unmute Voices')
                        }}</span>
                </div>
                <div @click="playerStore.toggleChordsOff()"
                    class="cursor-default flex items-center text-xs text-primary-muted border rounded p-0.5">
                    <span v-if="!playerStore.chordsOff">{{ t('Mute Backing') }}</span><span v-else>
                        {{ t('Unmute Backing')
                        }}</span>
                </div>

            </div>
            <Button v-if="user?.is_pro" class="absolute right-0 bottom-1 rounded-full size-3 cursor-pointer"
                @click="isExpanded = !isExpanded" variant="ghost">
                <Plus v-if="!isExpanded" class=" size-3" />
                <Minus v-else class=" size-3" />
            </Button>
        </div>

        <div v-else class="flex gap-2">
            <Button variant="outline" size="icon" @click="playerStore.toggleLoop()" class="cursor-pointer">
                <Repeat v-if="playerStore.isLooping" class="h-4 w-4" />
                <Forward v-else class="h-4 w-4" />
            </Button>
            <Button variant="outline" size="icon" @click="playerStore.play()" class="cursor-pointer">
                <Pause v-if="playerStore.isPlaying" class="h-4 w-4" />
                <Play v-else class="h-4 w-4" />
            </Button>
        </div>

        <div ref="audio" class="hidden" />
    </div>
</template>
