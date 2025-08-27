<script setup lang="ts">
import InputError from '@/components/laravel-starter-kit/InputError.vue';
import Accordion from '@/components/ui/accordion/Accordion.vue';
import AccordionContent from '@/components/ui/accordion/AccordionContent.vue';
import AccordionItem from '@/components/ui/accordion/AccordionItem.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
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
                <Accordion type="multiple" class="w-full" :unmountOnHide="false"
                    :modelValue="form.is_suggestion ? ['info'] : ['']">
                    <AccordionItem value="info">
                        <AccordionContent>
                            <div v-if="form.is_suggestion" class="space-y-2">
                                <Label>{{ t('What are you suggesting to modify?') }}</Label>
                                <RadioGroup v-model="form.suggestion_type" class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <RadioGroupItem value="tune" id="tune" />
                                        <Label for="title">{{ t('Title, Origin, Composer') }}</Label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <RadioGroupItem value="arrangement" id="arrangement" />
                                        <Label for="arrangement">{{ t('Other (rhythm, meter, notes, lyrics...)')
                                            }}</Label>
                                    </div>
                                </RadioGroup>
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>

                <Textarea v-model="form.content" :placeholder="t('Write your comment or suggestion here...')" />
                <InputError :message="form.errors.content" />
                <Button @click="onSubmit" :disabled="form.processing" class="cursor-pointer">{{ t('Submit') }}</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
