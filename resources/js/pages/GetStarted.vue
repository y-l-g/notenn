<script setup lang="ts">
import Render from '@/components/app/abcjs/Render.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import TunePlayer from '@/components/app/TunePlayer.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { usePlayerStore } from '@/Stores/player';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { renderAbc, TuneObject } from 'abcjs';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
defineOptions({ layout: PersistentLayout })

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Home'),
        href: '/',
    },
];
const textToTranslate =
    'Notenn ("note" in Breton) was born from a simple observation: musicians need a tool to share and organize their repertoire.'

const abc = 'T:Jingle Bells\n' +
    'K:D\n' +
    '"D"FF F2FF F2|FA D3/2E/2F4\n' +
    'w:Jin-gle bells, jin-gle bells, jin-gle all the way\n' +
    '|"G"GG G3/2G/2"D"GF FF/2F/2|"E7"FE EF"A7"E2 A2|]\n' +
    'w:oh what fun it is to ride in a one-horse o-pen sleigh. Hey!\n'

const visualObj = ref<TuneObject>(renderAbc('*', '')[0])
onMounted(() => {
    visualObj.value = usePlayerStore().synthControl.visualObj
}
)
</script>

<template>

    <Head :title="t('Welcome to Notenn')" />
    <AppLayout :breadcrumbs="breadcrumbs" no-sidebar>
        <Heading>{{ t('Welcome to Notenn - Collaborative music repertoire') }}</Heading>

        <HeadingSmall>{{ t('Project spirit') }}</HeadingSmall>
        <p>{{ t(textToTranslate) }}</p>
        <p>{{ t('Notenn currently only supports ABC notation.') }}</p>
        <p>{{ t('Unlike advanced notation tools, here we aim to:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Create a clean database of tunes') }}</li>
            <li>{{ t('Allow multiple arrangements for each tune') }}</li>
            <li>{{ t('Facilitate sharing for sessions, jam sessions, bands, fanfares and choirs') }}</li>
        </ul>

        <HeadingSmall>{{ t('Tune vs Arrangement') }}</HeadingSmall>
        <p class="font-medium">{{ t('A tune is the original work with:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('A title') }}</li>
            <li>{{ t('A composer (or "traditional")') }}</li>
            <li>{{ t('A geographical/cultural origin') }}</li>
        </ul>

        <p class="font-medium">{{ t('An arrangement is a specific version with:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('A key') }}</li>
            <li>{{ t('Melody notes') }}</li>
            <li>{{ t('A style/rhythm (waltz, bourrée, swing...)') }}</li>
            <li>{{ t('Optional chords') }}</li>
            <li>{{ t('Comments') }}</li>
        </ul>

        <HeadingSmall>{{ t('Example with Jingle Bells') }}</HeadingSmall>
        <p class="font-medium">{{ t('Tune:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Title: Jingle Bells') }}</li>
            <li>{{ t('Composer: James Lord Pierpont') }}</li>
            <li>{{ t('Origin: United States') }}</li>
        </ul>

        <p class="font-medium">{{ t('Simple arrangement:') }}</p>

        <pre class="bg-primary/10 p-2 rounded max-w-2xl overflow-auto whitespace-pre break-all text-sm">{{ abc }}</pre>
        <div class="max-w-2xl">
            <Render :abc-string="abc" setTune />
            <TunePlayer :tune-obj="visualObj" :is-current="true" />
        </div>

        <HeadingSmall>{{ t('Key Features') }}</HeadingSmall>
        <ul class="list-disc pl-5">
            <li>{{ t('Centralized database: One tune, multiple arrangements') }}</li>
            <li>{{ t('Suggestions: Propose corrections if a tune has errors') }}</li>
            <li>{{ t('Tunebooks: Create books for your group or project') }}</li>
            <li>{{ t('Collaborative: Like and comment on arrangements, tunes and tunebooks') }}</li>
            <li>{{ t('Direct score visualization, listening and editing') }}</li>
        </ul>

        <HeadingSmall>{{ t('For whom?') }}</HeadingSmall>
        <ul class="list-disc pl-5">
            <li>{{ t('Traditional musicians (Irish, Breton...)') }}</li>
            <li>{{ t('Jazz musicians and bands') }}</li>
            <li>{{ t('Fanfares and choirs') }}</li>
            <li>{{ t('Anyone who shares music in groups') }}</li>
        </ul>

        <HeadingSmall>{{ t('Next Steps') }}</HeadingSmall>
        <p>{{ t('Development in progress:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Enhanced listening (accompaniment styles)') }}</li>
            <li>{{ t('Adding audio/video recordings') }}</li>
            <li>{{ t('Tools for musical groups') }}</li>
        </ul>

        <HeadingSmall>{{ t('Support') }}</HeadingSmall>
        <p>{{ t('Notenn is free, but you can upgrade to Notenn Pro to:') }}</p>
        <ul class="list-disc pl-5">
            <li>{{ t('Support development and hosting') }}</li>
            <li>{{ t('Access advanced features') }}</li>
            <li>{{ t('Get more storage space') }}</li>
        </ul>

        <HeadingSmall>{{ t('Copyright') }}</HeadingSmall>
        <p>{{ t('Any removal request will be immediately applied.') }}</p>


        <p><em>{{ t('Last updated:') }} {{ (new Date(Date.UTC(2025, 6, 11))).toLocaleDateString(useI18n().locale.value)
        }}</em></p>
    </AppLayout>
</template>
