<script setup lang="ts">
import Accordion from '@/components/ui/accordion/Accordion.vue';
import AccordionContent from '@/components/ui/accordion/AccordionContent.vue';
import AccordionItem from '@/components/ui/accordion/AccordionItem.vue';
import AccordionTrigger from '@/components/ui/accordion/AccordionTrigger.vue';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';
import { Filter } from 'lucide-vue-next';
import Combobox from '../Combobox.vue';
import Input from '@/components/ui/input-custom/Input.vue';
import { useWindowSize } from '@vueuse/core';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
const { t } = useI18n();
const props = defineProps<{
    search?: string;
    rhythm?: string;
    origin?: string;
    composer?: string;
    best?: boolean,
    tunebook?: string,
    rhythms: Array<{ id: number; name: string }>;
    origins: Array<{ id: number; name: string }>;
    composers: Array<{ id: number; name: string }>;
    userLike?: boolean
    onlyUser?: boolean
}>();
const search = ref(props.search || '');
const selectedRhythm = ref(props.rhythm);
const selectedOrigin = ref(props.origin);
const selectedComposer = ref(props.composer);

const onlyUserRef = ref(props.onlyUser)
const userLikeRef = ref(props.userLike)
const bestRef = ref(props.best)

const composerOptions = computed(() => {
    return props.composers.map((composer) => ({
        value: composer.name,
        label: composer.name,
    }));
});
const rhythmOptions = computed(() => {
    return props.rhythms.map((rhythm) => ({
        value: rhythm.name,
        label: rhythm.name,
    }));
});
const originOptions = computed(() => {
    return props.origins.map((origin) => ({
        value: origin.name,
        label: origin.name,
    }));
});

const { width } = useWindowSize();
const inputWidth = computed(() => {
    return width.value < 900 ? 300 : 300;
});

const handleSearch = () => {
    if (props.tunebook) {
        bestRef.value = false;
    }
    router.get(
        route('arrangements.index'),
        {
            search: search.value,
            rhythm: selectedRhythm.value,
            origin: selectedOrigin.value,
            composer: selectedComposer.value,
            best: bestRef.value,
            tunebook: props.tunebook,
            onlyUser: onlyUserRef.value,
            userLike: userLikeRef.value,
            page: 1,
        },
        {
            preserveState: true,
            preserveScroll: true
        },
    );
};

const page = usePage();

watch(selectedRhythm, () => handleSearch());
watch(selectedOrigin, () => handleSearch());
watch(selectedComposer, () => handleSearch());
watch(onlyUserRef, () => {
    if (onlyUserRef.value) {
        bestRef.value = false
    }
    else {
        bestRef.value = true
    }
    handleSearch()
});
watch(userLikeRef, () => {
    if (userLikeRef.value) {
        bestRef.value = false
    }
    else {
        bestRef.value = true
    }
    handleSearch()
});
watch(search, () => handleSearch());
</script>

<template>
    <Accordion type="single" collapsible class="mb-4 w-full" :unmountOnHide="false" defaultValue="filters">
        <AccordionItem value="filters">
            <div class="max-w-[150px]">
                <AccordionTrigger class="text-sm font-medium cursor-pointer">
                    <template #icon>
                        <Filter class="h-4 w-4 shrink-0 text-muted-foreground transition-transform duration-200" />
                    </template>
                </AccordionTrigger>
            </div>
            <AccordionContent class="space-y-3 p-2">
                <div class="grid grid-cols-1 gap-2 lg:grid-cols-2 max-w-[640px]">
                    <Input v-model="search" :placeholder="t('Search')" clearable :width="inputWidth" />
                    <Combobox v-model:selectedValue="selectedRhythm" :options="rhythmOptions" :placeholder="t('Rhythm')"
                        :width="inputWidth" />
                    <Combobox v-model:selectedValue="selectedComposer" :options="composerOptions"
                        :placeholder="t('Composer')" :width="inputWidth" />
                    <Combobox v-model:selectedValue="selectedOrigin" :options="originOptions" :placeholder="t('Origin')"
                        :width="inputWidth" />
                    <div class="h-10 flex items-center" v-if="page.props.auth.user">
                        <Checkbox v-model="onlyUserRef" id="onlyUser" />
                        <label for="onlyUser" class="ml-2 text-sm text-muted-foreground">{{ t('My tunes')
                        }}</label>
                    </div>
                    <div class="h-10 flex items-center" v-if="page.props.auth.user">
                        <Checkbox v-model="userLikeRef" id="userLike" />
                        <label for="userLike" class="ml-2 text-sm text-muted-foreground">{{ t('Tunes i like')
                        }}</label>
                    </div>
                </div>
            </AccordionContent>
        </AccordionItem>
    </Accordion>
</template>
