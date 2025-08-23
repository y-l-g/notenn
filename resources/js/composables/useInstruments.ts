import { usePlayerStore } from '@/Stores/player';
import { ref } from 'vue';

export const useInstruments = () => {
    const playerStore = usePlayerStore();

    const loadingStates = ref({
        program: false,
        accProgram: false,
        bassProgram: false,
        swing: false,
        gchord: false,
        voices: false,
        chords: false,
    });

    const voicesOff = ref(false);
    const chordsOff = ref(false);

    const updateSynthSettings = async (updateFn: () => void, loadingKey: keyof typeof loadingStates.value): Promise<void> => {
        if (loadingStates.value[loadingKey]) return;

        loadingStates.value[loadingKey] = true;

        try {
            updateFn();

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
            loadingStates.value[loadingKey] = false;
        }
    };

    const setProgram = (program: number) =>
        updateSynthSettings(() => {
            playerStore.synthControl.options = {
                ...playerStore.synthControl.options,
                program,
            };
        }, 'program');

    const setAccProgram = (program: number) =>
        updateSynthSettings(() => {
            playerStore.synthControl.options = {
                ...playerStore.synthControl.options,
                chordprog: program,
            };
        }, 'accProgram');

    const setBassProgram = (program: number) =>
        updateSynthSettings(() => {
            playerStore.synthControl.options = {
                ...playerStore.synthControl.options,
                bassprog: program,
            };
        }, 'bassProgram');

    const toggleSwing = () =>
        updateSynthSettings(() => {
            playerStore.swing = !playerStore.swing;
            playerStore.synthControl.options = {
                ...playerStore.synthControl.options,
                swing: playerStore.swing ? 65 : 50,
            };
        }, 'swing');

    const setMidiGchord = (gchord: string) =>
        updateSynthSettings(() => {
            const newOptions = { ...playerStore.synthControl.options };
            if (gchord === 'bc') {
                delete newOptions.gchord;
            } else {
                newOptions.gchord = gchord;
            }
            playerStore.synthControl.options = newOptions;
        }, 'gchord');

    const toggleVoicesOff = () =>
        updateSynthSettings(() => {
            voicesOff.value = !voicesOff.value;
            playerStore.synthControl.options = {
                ...playerStore.synthControl.options,
                voicesOff: voicesOff.value,
            };
        }, 'voices');

    const toggleChordsOff = () =>
        updateSynthSettings(() => {
            chordsOff.value = !chordsOff.value;
            playerStore.synthControl.options = {
                ...playerStore.synthControl.options,
                chordsOff: chordsOff.value,
            };
        }, 'chords');

    return {
        setProgram,
        setAccProgram,
        setBassProgram,
        toggleSwing,
        setMidiGchord,
        toggleVoicesOff,
        voicesOff,
        toggleChordsOff,
        chordsOff,
    };
};
