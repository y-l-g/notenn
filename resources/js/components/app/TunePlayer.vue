<script setup lang="ts">
import { usePlayerStore } from '@/Stores/player';
import { Pause, Play } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { TuneObject } from 'abcjs';

const props = defineProps<{
    tuneObj: TuneObject;
    isCurrent: boolean;
}>();

const emit = defineEmits(['play']);

const playerStore = usePlayerStore();

const handlePlay = () => {
    if (props.isCurrent && playerStore.displayPlayer) {
        playerStore.play();
        return;
    }
    playerStore.setTune(props.tuneObj);
    playerStore.play();
    emit('play');
};

</script>

<template>
    <Button
        variant="outline"
        @click="handlePlay"
        size="icon"
        class="rounded-full self-center cursor-pointer text-muted-foreground"
    >
        <Pause v-if="playerStore.isPlaying && isCurrent" />
        <Play v-else />
    </Button>
</template>
