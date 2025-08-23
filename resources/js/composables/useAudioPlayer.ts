import { usePlayerStore } from '@/Stores/player';
import { ref, Ref } from 'vue';

interface AudioPlayerComposable {
    play: () => void;
    toggleLoop: () => void;
    seek: (percent: number) => void;
    isPlaying: Ref<boolean | undefined>;
    isLooping?: Ref<boolean | undefined>;
}

export const useAudioPlayer = (): AudioPlayerComposable => {
    const playerStore = usePlayerStore();
    const isPlaying = ref(false);
    const isLooping = ref<boolean | undefined>(false);

    const play = () => {
        if (!playerStore.synthControl.visualObj) return;
        playerStore.displayPlayer = true;
        playerStore.synthControl.play();
        isPlaying.value = !playerStore.synthControl.isStarted;
    };

    const toggleLoop = () => {
        playerStore.synthControl.toggleLoop();
        isLooping.value = playerStore.synthControl.isLooping;
    };

    const seek = (percent: number) => {
        playerStore.synthControl.seek(percent);
    };

    return {
        play,
        toggleLoop,
        seek,
        isPlaying,
        isLooping,
    };
};
