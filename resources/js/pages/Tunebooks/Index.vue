<script setup lang="ts">
import Description from '@/components/app/Description.vue';
import Heading from '@/components/app/laravel-starter-kit-customized/Heading.vue';
import ListItem from '@/components/app/ListItem.vue';
import Pagination from '@/components/app/Pagination.vue';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import Input from '@/components/ui/input-custom/Input.vue';
import { useRelativeTime } from '@/composables/useFormatDate';
import AppLayout from '@/layouts/AppLayout.vue';
import PersistentLayout from '@/layouts/PersistentLayout.vue';
import { BreadcrumbItem, MeilisearchPaginatedResponse, TunebookFromMeilisearch } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Heart, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
defineOptions({ layout: PersistentLayout })
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Tunebooks'),
        href: '/tunebooks',
    },
]
const props = defineProps<{
    tunebooks: MeilisearchPaginatedResponse<TunebookFromMeilisearch>,
    search?: string;
    userLike?: boolean
    onlyUser?: boolean
}>();

const onlyUserRef = ref(props.onlyUser)
const userLikeRef = ref(props.userLike)

const search = ref(props.search || '');

const onPageChange = (page: number) => {
    router.get(
        props.tunebooks.path,
        {
            page: page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
const handleSearch = () => {
    router.get(
        route('tunebooks.index'),
        {
            search: search.value,
            onlyUser: onlyUserRef.value,
            userLike: userLikeRef.value,
            page: 1,
        },
        {
            preserveState: true,
            preserveScroll: true
        },
    );
};
watch(search, () => handleSearch());
watch(onlyUserRef, () => handleSearch());
watch(userLikeRef, () => handleSearch());

const textToTranslate = "This action cannot be undone. This will permanently delete this tunebook"
const page = usePage();

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" noSidebar>
        <Heading>
            {{ t('Eplore Tunebook') }}
        </Heading>
        <Input v-model="search" :placeholder="t('Search')" clearable :width="260" />
        <div class="grid grid-cols-1 gap-2 lg:grid-cols-2 max-w-[640px]">
            <div class="h-10 flex items-center" v-if="page.props.auth.user">
                <Checkbox v-model="onlyUserRef" id="onlyUser" />
                <label for="onlyUser" class="ml-2 text-sm text-muted-foreground">{{ t('My tunebooks')
                    }}</label>
            </div>
            <div class="h-10 flex items-center" v-if="page.props.auth.user">
                <Checkbox v-model="userLikeRef" id="userLike" />
                <label for="userLike" class="ml-2 text-sm text-muted-foreground">{{ t('Tunebooks i like')
                    }}</label>
            </div>
        </div>
        <ul class="space-y-5">
            <ListItem class="space-y-3 relative" v-for="tunebook in tunebooks.data.hits" :key="tunebook.id">
                <div class="flex items-center gap-1 text-muted-foreground absolute right-3 top-2 m-0 size-9">
                    <span>{{ tunebook.likes_count }}</span>
                    <Heart class="size-4" />
                </div>
                <div class="flex justify-between">
                    <Link :href="route('arrangements.index', { tunebook: tunebook.name, user: '', best: false })"
                        prefetch class="font-medium">
                    <span v-html="tunebook._formatted.name"></span>
                    </Link>
                </div>
                <Description>{{ t('Added by') }} <span v-html="tunebook._formatted.user_name"></span></Description>
                <Description class="text-sm text-muted-foreground">{{ tunebook.arrangements_count }} {{
                    t('arrangements') }}
                </Description>
                <Description>
                    {{ t('Updated') }} {{ useRelativeTime().relativeTime(tunebook.updated_at) }}
                </Description>

                <div class="flex flex-wrap gap-2" v-if="tunebook.tags.length > 0">
                    <Link v-for="(tag, index) in tunebook._formatted.tags.slice(0, 6)" :key="tag" prefetch
                        class="text-xs bg-primary/40 px-2 text-primary-foreground dark:bg-primary/60 font-medium py-0.5 rounded-md"
                        :href="route('tunebooks.index', { search: tunebook.tags[index] })">
                    <span v-html="tag"></span>
                    </Link>
                </div>
                <AlertDialog v-if="page.props.auth.user?.id === tunebook.user_id">
                    <AlertDialogTrigger class="absolute right-3 bottom-2">
                        <Button variant="ghost" class="rounded-full cursor-pointer" size="icon">
                            <Trash2 class="text-muted-foreground" />
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
                            <AlertDialogAction @click="router.delete(route('tunebooks.destroy', tunebook.id))">{{
                                t('Continue') }}</AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </Listitem>
        </ul>
        <Pagination :data="tunebooks" @pageChange="onPageChange" />
    </AppLayout>
</template>
