import { usePlayerStore } from '@/Stores/player';
import { Ref, ref } from 'vue';

type DrumBeatKey = '1/4' | '3/4' | '4/4' | '5/4' | '6/4' | '7/4' | '5/8' | '6/8' | '7/8' | '9/8' | '10/8' | '11/8' | '12/8';

interface MetronomeComposable {
    metronome: Ref<boolean>;
    toggleMetronome: () => Promise<void>;
    setDrumPattern: () => void;
}

export const useMetronome = (): MetronomeComposable => {
    const drumBeats: Record<DrumBeatKey, string> = {
        '1/4': 'd 76 10',
        '3/4': 'ddd 76 77 77 10 10 10',
        '4/4': 'dddd 76 77 77 77 10 10 10 10',
        '5/4': 'ddddd 76 77 77 77 77 10 10 10 10 10',
        '6/4': 'dddddd 76 77 77 77 77 77 10 10 10 10 10',
        '7/4': 'ddddddd 76 77 77 77 77 77 77 10 10 10 10 10 10',
        '5/8': 'dzzdz 76 76 10 10',
        '6/8': 'dzzdzz 76 76 10 10',
        '7/8': 'dzdzdzz 76 76 76 10 10 10',
        '9/8': 'dzzdzzdzz 76 76 76 10 10 10',
        '10/8': 'dzzdzzdzdz 76 76 76 76 10 10 10 10',
        '11/8': 'dzzdzzdzdzz 76 76 76 76 10 10 10 10',
        '12/8': 'dzzdzzdzzdzz 76 76 76 76 10 10 10 10',
    } as const;

    const metronome = ref(false);
    const currentDrumPattern = ref('');
    const playerStore = usePlayerStore();

    const getMeterPattern = (): string => {
        if (!playerStore.synthControl.visualObj) return drumBeats['1/4'];
        const meterFraction = playerStore.synthControl.visualObj.getMeterFraction();
        const meterKey = `${meterFraction.num}/${meterFraction.den}`;
        return drumBeats[meterKey as DrumBeatKey] || drumBeats['4/4'];
    };
    const setDrumPattern = () => {
        currentDrumPattern.value = metronome.value ? getMeterPattern() : '';

        playerStore.synthControl.options = {
            ...playerStore.synthControl.options,
            drum: currentDrumPattern.value,
        };
    };
    const isToggling = ref(false);

    const toggleMetronome = async (): Promise<void> => {
        if (isToggling.value) return;

        isToggling.value = true;
        try {
            metronome.value = !metronome.value;
            setDrumPattern();
            const wasPlaying = playerStore.synthControl.isStarted;
            const startPercent = playerStore.synthControl.percent || 0;

            playerStore.synthControl.destroy();
            playerStore.synthControl.isStarted = false;

            await playerStore.synthControl.go();

            if (!playerStore.synthControl.midiBuffer?.duration) {
                console.log('no MidiBuffer or duration');
                return;
            }

            playerStore.synthControl.setProgress(startPercent, playerStore.synthControl.midiBuffer.duration * 1000);

            if (wasPlaying) {
                playerStore.synthControl.play();
            }

            playerStore.synthControl.seek(startPercent);
        } finally {
            isToggling.value = false;
        }
    };
    return {
        metronome,
        toggleMetronome,
        setDrumPattern,
    };
};
