<script setup lang="ts">
import InputError from '@/components/laravel-starter-kit/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Plus } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const emit = defineEmits(['submit']);

const form = defineModel('form', {
    type: Object,
});

const open = defineModel('open', {
    type: Boolean,
});

const onSubmit = () => {
    emit('submit', form.value);
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="ghost" size="sm" class="p-6 cursor-pointer">
                <Plus /><span class="text-wrap">
                    {{ t('Post a Comment or Suggestion') }}
                </span>
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('Post a Comment or Suggestion') }}</DialogTitle>
            </DialogHeader>
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <Label for="is_suggestion">{{ t('Is this a modification suggestion?') }}</Label>
                    <input id="is_suggestion" v-model="form.is_suggestion" type="checkbox" />
                </div>
                <Textarea v-model="form.content" :placeholder="t('Write your comment or suggestion here...')" />
                <InputError :message="form.errors.content" />
                <Button @click="onSubmit" :disabled="form.processing">{{ t('Submit') }}</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
