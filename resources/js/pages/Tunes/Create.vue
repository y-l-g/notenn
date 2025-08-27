<script setup lang="ts">
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { InertiaForm, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useWindowSize } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import FormField from '@/components/app/FormField.vue';
import Input from '@/components/ui/input-custom/Input.vue';
import Combobox from '@/components/app/Combobox.vue';
import AddComposerModal from '@/components/app/AddComposerModal.vue';
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
const props = defineProps<{
    composers: Array<{ id: number; name: string }>;
    origins: Array<{ id: number; name: string }>;
}>();

const form = useForm<InertiaForm<any>>({
    title: '',
    composer_id: <number | null>null,
    origin_id: <number | null>null,
});

const submit = (form: InertiaForm<any>) => {
    form.post('/tunes');
};

const { width } = useWindowSize()
const inputWidth = computed(() => {
    return width.value < 400 ? 200 : 270;
});

const selectedOrigin = ref(form.origin_id?.toString());
const selectedComposer = ref(form.composer_id?.toString());

const mapOptions = (items: Array<{ id: number, name: string }> | string[]) => items.map(item => ({
    value: typeof item === 'string' ? item : item.id.toString(),
    label: typeof item === 'string' ? item : item.name
}));

const composerOptions = computed(() => mapOptions(props.composers));
const originOptions = computed(() => mapOptions(props.origins));

watch(selectedComposer, (newValue) => {
    form.composer_id = newValue ? parseInt(newValue) : null;
});

watch(selectedOrigin, (newValue) => {
    form.origin_id = newValue ? parseInt(newValue) : null;
});


</script>

<template>

    <Head :title="t('New tune')" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading>
            {{ t('New tune') }}
        </Heading>
        <p>{{ t('Before create a new tune, please read') }}
            <Link class="underline" :href="route('getstarted')" prefetch>{{ t('Getting Started') }}. </Link>{{
                t('Then,') }}
            <Link class="underline" :href="route('arrangements.index')" prefetch>{{ t('search') }}</Link>
            {{ t("if the tune already exists already on the site and if so, fork it.") }}
        </p>
        <FormField description="T" :error="form.errors.title">
            <Input v-model="form.title" :placeholder="t('Title') + ' ' + t('(required)')" clearable
                :width="inputWidth" />
        </FormField>
        <FormField description="C" :error="form.errors.composer_id">
            <Combobox v-model:selectedValue="selectedComposer" :options="composerOptions"
                :placeholder="t('Composer') + ' ' + t('(optional)')" :width="inputWidth - 35" />
            <AddComposerModal />
        </FormField>
        <FormField description="O" :error="form.errors.origin_id">
            <Combobox v-model:selectedValue="selectedOrigin" :options="originOptions"
                :placeholder="t('Origin') + ' ' + t('(optional)')" :width="inputWidth" />
        </FormField>
        <div class="mt-4 flex">
            <Button type="button" class="px-6 cursor-pointer" variant="outline" @click="submit(form)"
                :disabled="form.processing">
                {{ t('Create Tune') }}
            </Button>
        </div>
    </AppLayout>
</template>
