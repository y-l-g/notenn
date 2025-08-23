<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n(); import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/laravel-starter-kit/InputError.vue';
import CreateTunebookModal from '../CreateTunebookModal.vue';
import Combobox from '../Combobox.vue';
import { Plus } from 'lucide-vue-next';

const props = defineProps<{
    tunebooks: Array<{ id: number; name: string }>;
    arrangementId: number;
}>();


const tunebookOptions = computed(() => {
    return props.tunebooks?.map((tunebook) => ({
        value: tunebook.id.toString(),
        label: tunebook.name,
    }));
});

const isOpen = ref(false);

const selectedTunebook = ref<string | undefined>(undefined);

const form = useForm({
    tunebook_id: null as number | null,
});

const submit = () => {
    form.post(
        route('arrangements.tunebooks.store', {
            arrangement: props.arrangementId,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                isOpen.value = false;
                form.reset();
            },
        },
    );
};

watch(
    selectedTunebook,
    (newValue) => {
        form.tunebook_id = newValue ? parseInt(newValue) : null;
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger asChild>
            <Button variant="ghost" size="sm">
                <Plus />
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle></DialogTitle>
            </DialogHeader>
            <p>{{ t('Add to Tunebook') }}</p>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="flex-col sm:flex space-y-4 sm:space-x-2">
                    <Combobox v-model:selectedValue="selectedTunebook" :options="tunebookOptions" :width="220"
                        :placeholder="t('Search tunebook...')" />
                    <CreateTunebookModal />
                </div>
                <InputError :message="form.errors.tunebook_id" />
                <Button type="submit" :disabled="form.processing">{{ t('Save') }}</Button>
            </form>
        </DialogContent>
    </Dialog>
</template>
