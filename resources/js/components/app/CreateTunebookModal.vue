<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import InputError from '../laravel-starter-kit/InputError.vue';
const { t } = useI18n();
const isOpen = ref(false);

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('tunebooks.store'), {
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger asChild>
            <Button variant="outline">{{ t('Add Tunebook') }}</Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('Add a Tunebook') }}</DialogTitle>
            </DialogHeader>
            <form
                @submit.prevent="submit"
                class="space-y-4"
            >
                <Input
                    v-model="form.name"
                    :placeholder="t('Tunebook Name')"
                />
                <InputError :message="form.errors.name" />
                <Button
                    type="submit"
                    :disabled="form.processing"
                >{{ t('Save') }}</Button>
            </form>
        </DialogContent>
    </Dialog>
</template>
