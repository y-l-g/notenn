// composables/useComments.ts
import { InertiaForm, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

export function useComments(arrangementId: number) {
    const open = ref(false);

    const form = useForm({
        content: '',
        is_suggestion: false as boolean,
        suggestion_type: '',
        arrangement_id: arrangementId,
    });

    const submitComment = (form: InertiaForm<any>) => {
        form.post(route('comments.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                open.value = false;
            },
        });
    };

    return {
        open,
        form,
        submitComment,
    };
}
