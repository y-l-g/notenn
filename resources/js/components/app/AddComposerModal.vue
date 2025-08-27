<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const isOpen = ref(false);

const form = useForm({
    name: '',
});

const submit = () => {
    form.post('/composers', {
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
            <Button variant="ghost" size="icon" class="text-muted-foreground ml-2 cursor-pointer">
                <Plus />
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('Add a Composer') }}</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-4">
                <Input v-model="form.name" :placeholder="t('Composer Name')" />
                <Button class="cursor-pointer" type="submit" :disabled="form.processing">{{ t('Save') }}</Button>
            </form>
        </DialogContent>
    </Dialog>
</template>
