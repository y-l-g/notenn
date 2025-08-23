import { useAudioPlayer } from '@/composables/useAudioPlayer';
import { useInstruments } from '@/composables/useInstruments';
import { useMetronome } from '@/composables/useMetronome';
import { useTimeManager } from '@/composables/useTimeManager';
import abcjs, { renderAbc, TuneObject } from 'abcjs';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useCursor } from '../composables/useCursor';

export const usePlayerStore = defineStore('player', () => {
    const visualObjRef = ref(renderAbc('*', '')[0]);
    const targetElement = ref<HTMLElement | null>(null);
    const synthControl = ref(new abcjs.synth.SynthController());
    const { play, toggleLoop, seek, isPlaying, isLooping } = useAudioPlayer();
    const percent = ref(0);
    const breakPointSidebar = ref(1280);
    const percentArray = computed({
        get: () => [percent.value],
        set: (newValue: number[]) => seek(newValue[0]),
    });
    const displayCursor = ref(false);
    const displayPlayer = ref(false);
    const swing = ref(false);
    const { clock, setClock } = useTimeManager();
    const { cursorControl, toggleCursor } = useCursor();
    const { metronome, toggleMetronome, setDrumPattern } = useMetronome();
    const { setProgram, setAccProgram, setBassProgram, toggleSwing, setMidiGchord, toggleVoicesOff, voicesOff, toggleChordsOff, chordsOff } =
        useInstruments();
    synthControl.value.options = {
        ...synthControl.value.options,
        soundFontUrl: 'https://paulrosen.github.io/midi-js-soundfonts/MusyngKite/',
    };
    const setTune = (visualObj: TuneObject) => {
        visualObjRef.value = visualObj;
        synthControl.value.setTune(visualObj, true, synthControl.value.options);
        setDrumPattern();
        isPlaying.value = false;
    };

    const closeSynth = () => {
        visualObjRef.value = renderAbc('*', '')[0];
        setTune(visualObjRef.value);
        displayPlayer.value = false;
    };

    return {
        targetElement,
        synthControl,
        cursorControl,
        percent,
        percentArray,
        displayPlayer,
        play,
        isPlaying,
        isLooping,
        toggleLoop,
        metronome,
        toggleMetronome,
        clock,
        setClock,
        setDrumPattern,
        setTune,
        closeSynth,
        visualObjRef,
        displayCursor,
        toggleCursor,
        setProgram,
        setAccProgram,
        setBassProgram,
        swing,
        toggleSwing,
        setMidiGchord,
        toggleVoicesOff,
        voicesOff,
        toggleChordsOff,
        chordsOff,
        breakPointSidebar,
    };
});
