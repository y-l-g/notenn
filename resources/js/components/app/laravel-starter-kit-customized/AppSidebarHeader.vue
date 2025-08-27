<script setup lang="ts">
import Breadcrumbs from '@/components/app/laravel-starter-kit-customized/Breadcrumbs.vue';
import Button from '@/components/ui/button/Button.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Languages, MoonStar, Sun } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import { useWindowSize } from '@vueuse/core';
import { useColorMode } from '@vueuse/core';
const STORAGE_KEY = 'user_locale';
withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(), {
    breadcrumbs: () => []
});
const { width } = useWindowSize()
const { locale } = useI18n();
const changeLocale = (language: string) => {
    localStorage.setItem(STORAGE_KEY, language);
    locale.value = language;
    router.post(
        route('lang', { lang: language }),
        {},
        {
            preserveState: false,
            preserveScroll: true,
        },
    );
};
const mode = useColorMode();
</script>

<template>
    <header
        class="sticky top-0 z-1 px-2 sm:px-4 md:px-6 lg:px-8 flex h-16 justify-between shrink-0 items-center gap-2 border-b bg-background/80 backdrop-blur-sm  transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 rounded-t-xl">
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <Breadcrumbs v-if="breadcrumbs.length > 0 && width > 350" :breadcrumbs="breadcrumbs" />
        </div>
        <div class="flex items-center gap-1">
            <Button v-if="mode === 'light'" @click="mode = 'dark'" variant="ghost" class="cursor-pointer">
                <MoonStar />
            </Button>
            <Button v-if="mode == 'dark'" @click="mode = 'light'" variant="ghost" class="cursor-pointer">
                <Sun />
            </Button>
            <DropdownMenu>
                <DropdownMenuTrigger>
                    <Button size="icon" variant="ghost" class="cursor-pointer">
                        <Languages />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem>
                        <Button variant="ghost" @click="changeLocale('fr')" class="cursor-pointer"> Français </Button>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <Button variant="ghost" @click="changeLocale('en')" class="cursor-pointer"> English </Button>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <Button variant="ghost" @click="changeLocale('es')" class="cursor-pointer"> Español </Button>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <Button variant="ghost" @click="changeLocale('br')" class="cursor-pointer"> Brezhoneg </Button>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
