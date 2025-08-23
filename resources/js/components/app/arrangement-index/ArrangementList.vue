<script setup lang="ts">
import { Arrangement, ArrangementFromMeilisearch, Tunebook } from '@/types';
import { ref } from 'vue';
import TuneListItem from './ArrangementListItem.vue';
import Description from '../Description.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineProps<{
    arrangements: ArrangementFromMeilisearch[];
    tunebook?: Tunebook;
}>();
const currentArrangement = ref<Arrangement | null>(null);

</script>

<template>
    <ul class="space-y-5">
        <TuneListItem v-for="arrangement in arrangements" :key="arrangement.id" :arrangement="arrangement"
            :is-current="currentArrangement?.id === arrangement.id" :tunebook="tunebook" />
    </ul>
    <Description v-if="arrangements.length == 0">{{ t('No tunes found') }}</Description>
</template>
