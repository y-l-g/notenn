import { useScroll } from '@vueuse/core';
import { defineStore } from 'pinia';
import { nextTick, onMounted, ref, watch } from 'vue';

export const useArrangementIndex = defineStore('arrangementIndex', () => {
    const currentArrangementId = ref<number | null>(null);
    const arrangementIds = ref<number[]>([]);
    const renderRefs = ref<{ [key: string | number]: any }>({});
    const location = ref('');
    const activeItemId = ref<number | null>(null);
    const itemRefs = ref<{ [key: string | number]: any }>({});
    const lastClickedId = ref<number | null>(null);
    const clickTimestamp = ref(0);

    arrangementIds.value.forEach((id) => {
        itemRefs.value[id] = null;
    });

    const checkVisibility = () => {
        if (Date.now() - clickTimestamp.value < 1000 && lastClickedId.value !== null) {
            activeItemId.value = lastClickedId.value;
            return;
        }

        const { scrollY: scrollPosition, innerHeight: windowHeight } = window;
        const documentHeight = document.documentElement.scrollHeight;
        const windowCenter = scrollPosition + windowHeight / 2;
        if (scrollPosition <= 0) {
            activeItemId.value = arrangementIds.value[0];
            return;
        }
        if (scrollPosition + windowHeight >= documentHeight - 1) {
            activeItemId.value = arrangementIds.value.at(-1) || null;
            return;
        }
        const items = [];
        for (const [id, el] of Object.entries(itemRefs.value)) {
            if (!el) continue;

            const rect = el.getBoundingClientRect();
            const elementTop = scrollPosition + rect.top;
            const elementBottom = scrollPosition + rect.bottom;

            items.push({
                id: Number(id),
                top: elementTop,
                bottom: elementBottom,
                center: (elementTop + elementBottom) / 2,
                isVisible: rect.bottom > 0 && rect.top < windowHeight,
            });
        }
        let bestMatch = items
            .filter((item) => item.isVisible)
            .sort((a, b) => Math.abs(a.center - windowCenter) - Math.abs(b.center - windowCenter))[0];
        if (!bestMatch) {
            bestMatch = items.filter((item) => item.bottom <= scrollPosition + windowHeight).sort((a, b) => b.bottom - a.bottom)[0];
        }
        if (bestMatch) {
            activeItemId.value = bestMatch.id;
        }
    };
    const { x, y } = useScroll(window, { throttle: 100 });

    watch([x, y], () => {
        checkVisibility();
    });

    const scrollToItem = (id: number) => {
        const element = itemRefs.value[id as any];
        if (element) {
            lastClickedId.value = id;
            clickTimestamp.value = Date.now();
            activeItemId.value = id;
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    onMounted(() => {
        nextTick(() => {
            checkVisibility();
        });
    });

    return {
        currentArrangementId,
        arrangementIds,
        renderRefs,
        scrollToItem,
        activeItemId,
        itemRefs,
        location,
    };
});
