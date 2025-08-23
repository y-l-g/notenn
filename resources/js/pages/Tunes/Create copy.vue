<script setup lang="ts">
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { InertiaForm, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import Accordion from '@/components/ui/accordion/Accordion.vue';
import AccordionContent from '@/components/ui/accordion/AccordionContent.vue';
import AccordionItem from '@/components/ui/accordion/AccordionItem.vue';
import { useWindowSize } from '@vueuse/core';
import { AccordionHeader, AccordionTrigger } from 'reka-ui';
import { ChevronDown } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import TuneFormFields from '@/components/app/tunes-create/TuneFormFields.vue';
import Editor from '@/components/app/abcjs/Editor.vue';
import { usePlayerStore } from '@/Stores/player';
defineOptions({ layout: PersistentLayout })
const { t } = useI18n();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Tunes'),
        href: '/tunes',
    },
    {
        title: t('Create'),
        href: '/tunes/create',
    },
];
defineProps<{
    composers: Array<{ id: number; name: string }>;
    rhythms: Array<{ id: number; name: string }>;
    meters: Array<{ id: number; name: string }>;
    origins: Array<{ id: number; name: string }>;
}>();

const { width } = useWindowSize()

const form = useForm<InertiaForm<any>>({
    title: '',
    name: '',
    composer_id: <number | null>null,
    origin_id: <number | null>null,
    meter_id: <number | null>null,
    tempo: '',
    parts: '',
    transcription: '',
    key: '',
    words: '',
    notes: '',
    rhythm_id: <number | null>null,
    tune_body: '',
});

const submit = (form: InertiaForm<any>) => {
    form.post('/tunes');
};

const hasInfoErrors = computed(() => {
    return Boolean(
        form.errors.title ||
        form.errors.composer_id ||
        form.errors.rhythm_id ||
        form.errors.origin_id ||
        form.errors.parts ||
        form.errors.meter_id ||
        form.errors.tempo ||
        form.errors.transcription ||
        form.errors.key ||
        form.errors.mode ||
        form.errors.words
    );
});

</script>

<template>

    <Head :title="t('New tune')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>
            {{ t('New tune') }}
        </Heading>
        <p>Before create a new tune, please read the
            <Link class="underline" :href="route('getstarted')" prefetch>{{ t('Get Started') }} </Link> page, and
            <Link class="underline" :href="route('arrangements.index')" prefetch>search</Link> if it exists already on
            the site
            and if so, fork it.
        </p>
        <Accordion v-if="width < usePlayerStore().breakPointSidebar" type="multiple" :defaultValue="['info']">
            <AccordionItem value="info" class="border-0">
                <AccordionHeader class="flex">
                    <AccordionTrigger data-slot="accordion-trigger"
                        class="flex flex-1 items-start gap-4 py-4 transition-all outline-none [&[data-state=open]>svg]:rotate-180 cursor-pointer">
                        <HeadingSmall>{{ t('Information fields') }}</HeadingSmall>
                        <span v-if="hasInfoErrors" class="text-red-500">*</span>
                        <ChevronDown
                            class="text-muted-foreground size-4 shrink-0 translate-y-0.5 transition-transform duration-200" />
                    </AccordionTrigger>
                </AccordionHeader>

                <AccordionContent>
                    <TuneFormFields :form :composers :meters :rhythms :origins class="grid lg:grid-cols-2">
                    </TuneFormFields>
                </AccordionContent>
            </AccordionItem>
        </Accordion>
        <template #sidebar>
            <Accordion type="multiple" :defaultValue="['info']">
                <AccordionItem value="info" class="border-0">
                    <AccordionHeader class="flex">
                        <AccordionTrigger data-slot="accordion-trigger"
                            class="flex flex-1 items-start gap-4 py-4 transition-all outline-none [&[data-state=open]>svg]:rotate-180 cursor-pointer">
                            <HeadingSmall>{{ t('Information fields') }}</HeadingSmall>
                            <span v-if="hasInfoErrors" class="text-red-500">*</span>
                            <ChevronDown
                                class="text-muted-foreground size-4 shrink-0 translate-y-0.5 transition-transform duration-200" />
                        </AccordionTrigger>
                    </AccordionHeader>

                    <AccordionContent>
                        <TuneFormFields :form :composers :meters :rhythms :origins>
                        </TuneFormFields>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </template>

        <div class="space-y-2">
            <HeadingSmall>{{ t('ABC Notation') }}</HeadingSmall>
            <Editor :form :composers :meters :rhythms :origins />
        </div>
        <!-- <div v-if="form.words" class="mb-2 flex items-center p-2">
            <Checkbox v-model="showWords" id="show-words" />
            <label for="show-words" class="ml-2 text-sm text-gray-600">{{ t('Show words') }}</label>
        </div> -->

        <div class="mt-4 flex">
            <Button type="button" class="px-6" variant="outline" @click="submit(form)">
                {{ t('Create Tune') }}
            </Button>
        </div>
    </AppLayout>
</template>
