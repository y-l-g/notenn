import { usePlayerStore } from '@/Stores/player';
import { CursorControl, NoteTimingEvent } from 'abcjs';
import { ref } from 'vue';

interface Position {
    x: number;
    y: number;
    height: number;
}

export const useCursor = () => {
    const playerStore = usePlayerStore();
    const cursor = ref<SVGLineElement | null>(null);
    const totalTimeHelper = ref(0);
    let lastScrollTime = 0;

    const isElementInDOM = (el: Element | null): boolean => {
        if (!el) return false;
        try {
            return document.body.contains(el);
        } catch (e) {
            console.log(e);
            return false;
        }
    };
    const hasClosedParent = (el: HTMLElement | null): boolean => !!el && (el.dataset?.state === 'closed' || hasClosedParent(el.parentElement));

    const scrollToCursor = (position: Position): void => {
        if (
            !playerStore.targetElement ||
            !playerStore.displayCursor ||
            !isElementInDOM(playerStore.targetElement) ||
            hasClosedParent(playerStore.targetElement)
        ) {
            return;
        }
        const now = Date.now();
        if (now - lastScrollTime < 100) return;
        lastScrollTime = now;
        const svg = playerStore.targetElement.querySelector('svg');
        if (!svg) return;
        const svgRect = svg.getBoundingClientRect();
        const cursorTop = position.y + svgRect.top + window.scrollY;
        const cursorBottom = cursorTop + position.height;
        const viewportTop = window.scrollY;
        const viewportBottom = viewportTop + window.innerHeight;
        const buffer = window.innerHeight * 0.3;
        if (cursorTop < viewportTop + buffer || cursorBottom > viewportBottom - buffer) {
            const targetScroll = cursorTop - window.innerHeight / 2;
            window.scrollTo({
                top: targetScroll,
                behavior: 'smooth',
            });
        }
    };

    const resetCursor = (): void => {
        if (cursor.value) {
            cursor.value.remove();
            cursor.value = null;
        }
    };
    const createCursor = (): boolean => {
        if (!playerStore.targetElement) return false;
        const svg = playerStore.targetElement.querySelector('svg');
        if (!svg) return false;
        resetCursor();
        cursor.value = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        if (playerStore.displayCursor) {
            cursor.value.setAttribute('class', 'tune-cursor stroke-1');
        } else {
            cursor.value.setAttribute('class', 'tune-cursor stroke-0');
        }
        svg.appendChild(cursor.value);
        return true;
    };

    const updateCursorPosition = (position: Position): void => {
        if (!cursor.value && !createCursor()) return;
        cursor.value!.setAttribute('x1', String(position.x));
        cursor.value!.setAttribute('x2', String(position.x));
        cursor.value!.setAttribute('y1', String(position.y));
        cursor.value!.setAttribute('y2', String(position.y + position.height));
        if (playerStore.displayCursor) {
            scrollToCursor(position);
        }
    };

    const toggleCursor = (): void => {
        if (!cursor.value) return;
        if (playerStore.displayCursor) {
            cursor.value.setAttribute('class', 'tune-cursor stroke-0');
        }
        if (!playerStore.displayCursor) {
            cursor.value.setAttribute('class', 'tune-cursor stroke-1');
        }
        playerStore.displayCursor = !playerStore.displayCursor;
    };

    const cursorControl: CursorControl = {
        onBeat: (beatNumber: number, totalBeats: number, totalTime: number, position: NoteTimingEvent): void => {
            playerStore.percent = beatNumber / totalBeats;
            playerStore.setClock(totalTime, playerStore.percent);
            totalTimeHelper.value = totalTime;

            if (position?.left && position?.top && position?.height) {
                updateCursorPosition({
                    x: position.left - 10,
                    y: position.top,
                    height: position.height,
                });
            } else {
                updateCursorPosition({
                    x: 0,
                    y: 0,
                    height: 0,
                });
            }
        },
        onStart: (): void => {
            createCursor();
        },
        onFinished: (): void => {
            playerStore.percent = 0;
            playerStore.isPlaying = false;
            playerStore.setClock(totalTimeHelper.value, 0);
        },
    };

    // onUnmounted(() => {
    //     resetCursor();
    // });

    return {
        cursorControl,
        resetCursor,
        toggleCursor,
    };
};
