<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import HeadingSmall from '@/components/app/laravel-starter-kit-customized/HeadingSmall.vue';
import InputError from '@/components/laravel-starter-kit/InputError.vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Password settings'),
        href: '/settings/password',
    },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" no-sidebar>

        <Head title="Password settings" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall>
                    {{ t('Update password') }}
                    <template #description>
                        {{ t("Ensure your account is using a long, random password to stay secure") }}
                    </template>
                </HeadingSmall>
                <form method="POST" @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="current_password">{{ t('Current password') }}</Label>
                        <Input id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                            type="password" class="mt-1 block w-full" autocomplete="current-password"
                            :placeholder="t('Current password')" />
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">{{ t('New password') }}</Label>
                        <Input id="password" ref="passwordInput" v-model="form.password" type="password"
                            class="mt-1 block w-full" autocomplete="new-password" :placeholder="t('New password')" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">{{ t('Confirm password') }}</Label>
                        <Input id="password_confirmation" v-model="form.password_confirmation" type="password"
                            class="mt-1 block w-full" autocomplete="new-password"
                            :placeholder="t('Confirm password')" />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ t('Save password') }}</Button>

                        <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ t('Saved') }}.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
