<script setup lang="ts">
import { ArrangementFromMeilisearch, Tunebook } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Heart, Pause, Play } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import ListItem from '../ListItem.vue';
import Description from '../Description.vue';
import Accordion from '@/components/ui/accordion/Accordion.vue';
import AccordionItem from '@/components/ui/accordion/AccordionItem.vue';
import AccordionTrigger from '@/components/ui/accordion/AccordionTrigger.vue';
import AccordionContent from '@/components/ui/accordion/AccordionContent.vue';
import Render from '../abcjs/Render.vue';
import { useAbcNotation } from '@/composables/useAbcNotation';
import { useArrangementIndex } from '@/Stores/arrangementIndex';
import { usePlayerStore } from '@/Stores/player';
import Button from '@/components/ui/button/Button.vue';
import DeleteArrangementModal from '../DeleteArrangementModal.vue';
const { t } = useI18n();
const props = defineProps<{
    arrangement: ArrangementFromMeilisearch;
    isCurrent: boolean;
    tunebook?: Tunebook;
}>();

const page = usePage();

const playerStore = usePlayerStore();

const handlePlay = (arrangementId: number) => {
    playerStore.targetElement = useArrangementIndex().renderRefs[arrangementId].target;
    if (arrangementId === useArrangementIndex().currentArrangementId && playerStore.displayPlayer) {
        playerStore.play();
        return;
    }
    playerStore.setTune(useArrangementIndex().renderRefs[arrangementId].tune);
    playerStore.play();
    useArrangementIndex().currentArrangementId = arrangementId;
};

const removeArrangementFromTunebook = () => {
    router.delete(route('arrangements.tunebooks.destroy', { arrangement: props.arrangement.id, tunebook: props.tunebook?.id }), {
        preserveScroll: true,
    });
};
</script>
<template>
    <div :ref="el => useArrangementIndex().itemRefs[arrangement.id] = el" class="scroll-mt-20">
        <ListItem class="relative">
            <div class="flex justify-between">

                <Link :href="route('arrangements.show', { arrangement: arrangement.id })" class="font-medium" prefetch>
                <span v-html="arrangement._formatted.title"></span>
                </Link>
                <div class="flex items-center gap-3">
                    <p class="flex items-center gap-1 text-sm mr-2 ml-3">
                        {{ arrangement.likes_count }}
                        <Heart class="size-4" />
                    </p>
                    <Button variant="outline" @click="handlePlay(arrangement.id)" size="icon"
                        class="rounded-full self-center cursor-pointer text-muted-foreground">
                        <Pause
                            v-if="playerStore.isPlaying && arrangement.id === useArrangementIndex().currentArrangementId" />
                        <Play v-else />
                    </Button>
                </div>
            </div>

            <Description v-if="arrangement.composer_name">
                <Link :href="route('arrangements.index', { composer: arrangement.composer_name })" prefetch>
                {{ t('Composer') }} : <span v-html="arrangement._formatted.composer_name"></span>
                </Link>

            </Description>
            <Description v-if="arrangement.rhythm_name">
                <Link :href="route('arrangements.index', { rhythm: arrangement.rhythm_name })" prefetch>
                {{ t('Rhythm') }} : <span v-html="arrangement._formatted.rhythm_name"></span>
                </Link>

            </Description>
            <Description v-if="arrangement.origin_name">
                <Link :href="route('arrangements.index', { origin: arrangement.origin_name })" prefetch>
                {{ t('Origin') }} : <span v-html="arrangement._formatted.origin_name"></span>
                </Link>
            </Description>
            <div class="flex flex-wrap gap-2" v-if="arrangement.tags.length > 0">
                <Link v-for="(tag, index) in arrangement._formatted.tags.slice(0, 6)" :key="tag" prefetch
                    class="text-xs bg-primary/40 px-2 text-primary-foreground dark:bg-primary/60 font-medium py-0.5 rounded-md"
                    :href="route('arrangements.index', { search: arrangement.tags[index] })">
                <span v-html="tag"></span>
                </Link>
            </div>

            <Accordion type="single" collapsible class="w-full" :unmountOnHide="false" defaultValue="[]">
                <AccordionItem value="score">
                    <AccordionTrigger class="text-sm text-muted-foreground justify-normal cursor-pointer">
                        {{ t('Show partition') }}
                    </AccordionTrigger>
                    <AccordionContent class="space-y-3 p-2">
                        <Render
                            :abc-string="useAbcNotation(arrangement).abcNotationWithoutWordsAndNotesAndTranscription"
                            :set-tune="arrangement.id === useArrangementIndex().currentArrangementId"
                            :ref="el => useArrangementIndex().renderRefs[arrangement.id] = el" />
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
            <div class=" flex justify-between">
                <Button v-if="tunebook && page.props.auth.user?.id === tunebook.user.id"
                    @click="removeArrangementFromTunebook" variant="ghost" class="cursor-pointer">
                    {{ t('Remove from Tunebook') }}
                </Button>
                <span v-else />
                <DeleteArrangementModal :arrangement="arrangement" />
            </div>
        </ListItem>
    </div>
</template>
