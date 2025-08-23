<script setup lang="ts">
import { Trash2 } from 'lucide-vue-next';
import Button from '../ui/button/Button.vue';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';

import { useI18n } from 'vue-i18n';
import { router, usePage } from '@inertiajs/vue3';
const { t } = useI18n();

defineProps<{
    arrangement: any;
}>();

const page = usePage();

const textToTranslate = "If this arrangement belongs to other users tunebooks, it won't be deleted but you won't own this arrangement anymore."

</script>

<template>
    <AlertDialog v-if="page.props.auth.user?.id === arrangement.user_id">
        <AlertDialogTrigger asChild>
            <Button variant="ghost" class="cursor-pointer" size="sm">
                <Trash2 />
            </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ t('Are you absolutely sure?') }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ t(textToTranslate) }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>{{ t('Cancel') }}</AlertDialogCancel>
                <AlertDialogAction @click="router.delete(route('arrangements.destroy', arrangement.id))">{{
                    t('Continue') }}</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
