<script setup lang="ts">
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { Arrangement, BreadcrumbItem } from '@/types';
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
import { useAbcNotation } from '@/composables/useAbcNotation';
import Description from '@/components/app/Description.vue';
import { usePlayerStore } from '@/Stores/player';
import { TagsInput } from '@/components/ui/tags-input';
import TagsInputItem from '@/components/ui/tags-input/TagsInputItem.vue';
import TagsInputItemText from '@/components/ui/tags-input/TagsInputItemText.vue';
import TagsInputItemDelete from '@/components/ui/tags-input/TagsInputItemDelete.vue';
import TagsInputInput from '@/components/ui/tags-input/TagsInputInput.vue';
import DeleteArrangementModal from '@/components/app/DeleteArrangementModal.vue';

const { t } = useI18n();
const props = defineProps<{
    composers: Array<{ id: number; name: string }>;
    rhythms: Array<{ id: number; name: string }>;
    meters: Array<{ id: number; name: string }>;
    origins: Array<{ id: number; name: string }>;
    arrangement: Arrangement
}>();
defineOptions({ layout: PersistentLayout })
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.arrangement.tune.title,
        href: route('arrangements.show', props.arrangement.id),
    },
    {
        title: t('Edit'),
        href: route('arrangements.edit', props.arrangement.id),
    },
];

const { width } = useWindowSize()

const tags = computed(() => {
    return props.arrangement.tags.map(tag => (tag.name));
});
console.log('tags', tags.value);

const form = useForm<InertiaForm<any>>({
    title: props.arrangement.tune.title,
    name: props.arrangement.name ?? '',
    tags: tags.value ?? [],
    composer_id: props.arrangement.tune.composer?.id ?? null,
    origin_id: props.arrangement.tune.origin?.id ?? null,
    meter_id: props.arrangement.meter?.id ?? null,
    tempo: props.arrangement.tempo ?? '120',
    parts: props.arrangement.parts ?? '',
    transcription: props.arrangement.transcription ?? '',
    key: props.arrangement.key ?? '',
    words: useAbcNotation(props.arrangement).words ?? '',
    notes: useAbcNotation(props.arrangement).notes ?? '',
    rhythm_id: props.arrangement.rhythm?.id ?? null,
    tune_body: useAbcNotation(props.arrangement).tuneBody ?? '',
});

const submit = (form: InertiaForm<any>) => {
    form.patch(route('arrangements.update', props.arrangement.id), {
        onSuccess: () => {
            form.reset();
        },
        preserveScroll: true,
    });
};

const hasInfoErrors = computed(() => {
    return Boolean(
        form.errors.rhythm_id ||
        form.errors.parts ||
        form.errors.meter_id ||
        form.errors.tempo ||
        form.errors.transcription ||
        form.errors.key ||
        form.errors.mode ||
        form.errors.words
    );
});

const textToTranslate = "Title, origin and composer can't be modified because they belong to the Tune. You can suggest a modification"


</script>

<template>

    <Head :title="t('Edit Arrangement')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>
            {{ t('Edit Arrangement') }}
        </Heading>
        <Description>
            <b>{{ t('Title') }}</b> : {{ form.title }}<br />
            <b>{{ t('Origin') }}</b> : {{form.origin_id ? props.origins.find(o => o.id === form.origin_id)?.name :
                t('Undefined')}}<br />
            <b>{{ t('Composer') }}</b> : {{form.composer_id ? props.composers.find(c => c.id === form.composer_id)?.name
                :
                t('Undefined')}}<br />
        </Description>
        <Description>
            {{ t(textToTranslate) }}
            <Link class="underline" prefetch
                :href="route('arrangements.show', { arrangement: arrangement.id, suggestion: true })">{{ t('here') }}
            </Link>.
        </Description>
        <HeadingSmall>{{ t('Tags') }}</HeadingSmall>
        <TagsInput v-model="form.tags">
            <TagsInputItem v-for="item in form.tags" :key="item" :value="item">
                <TagsInputItemText />
                <TagsInputItemDelete />
            </TagsInputItem>

            <TagsInputInput placeholder="Tag (press Enter to add a Tag)..." />
        </TagsInput>
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

                <AccordionContent class="space-y-2 max-w-sm">
                    <TuneFormFields :form :composers :meters :rhythms :origins editForm></TuneFormFields>
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

                    <AccordionContent class="space-y-2 max-w-sm">
                        <TuneFormFields :form :composers :meters :rhythms :origins editForm></TuneFormFields>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </template>

        <div class="space-y-2">
            <HeadingSmall>{{ t('ABC Notation') }}</HeadingSmall>
            <Editor :form :composers :meters :rhythms :origins />
        </div>

        <div class="mt-4 flex gap-5 items-center">
            <Button type="button" class="px-6 cursor-pointer" variant="outline" @click="submit(form)"
                :disabled="form.processing">
                {{ t('Update Arrangement') }}
            </Button>
            <DeleteArrangementModal :arrangement="arrangement" />
        </div>
    </AppLayout>
</template>
