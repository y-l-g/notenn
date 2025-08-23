import { ref, Ref } from 'vue';

interface TimeManagerComposable {
    clock: Ref<string>;
    setClock: (totalTime: number, percent: number) => void;
}

export const useTimeManager = (): TimeManagerComposable => {
    const clock = ref('');

    const setClock = (totalTime: number, percent: number) => {
        const elapsedSeconds = (totalTime * percent) / 1000;
        const elapsedMinutesDisplay = Math.floor(elapsedSeconds / 60);
        const elapsedSecondsDisplay = Math.floor(elapsedSeconds % 60);
        const elapsedSecondsFormatted = elapsedSecondsDisplay < 10 ? '0' + elapsedSecondsDisplay : elapsedSecondsDisplay.toString();

        const totalSeconds = totalTime / 1000;
        const totalMinutesDisplay = Math.floor(totalSeconds / 60);
        const totalSecondsDisplay = Math.floor(totalSeconds % 60);
        const totalSecondsFormatted = totalSecondsDisplay < 10 ? '0' + totalSecondsDisplay : totalSecondsDisplay.toString();

        clock.value = `${elapsedMinutesDisplay}:${elapsedSecondsFormatted}/${totalMinutesDisplay}:${totalSecondsFormatted}`;
    };

    return {
        clock,
        setClock,
    };
};
