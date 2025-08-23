<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { BriefcaseBusiness, LogIn, MapPin, NotepadText, Rss, Star } from 'lucide-vue-next';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMatchUrl } from '@/composables/useMatchUrl';
const { t } = useI18n();
interface Props {
    items: NavItem[];
    class?: string;
}
const page = usePage()

defineProps<Props>();
</script>

<template>
    <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`">
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child>
                        <a :href="item.href" target="_blank" rel="noopener noreferrer">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem v-if="!page.props.auth.user">
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child>
                        <Link href="/login" prefetch>
                        <LogIn />
                        <span>{{ t('Login / Register') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem v-if="!(page.props.auth.user && page.props.auth.user.is_pro)">
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child :is-active="useMatchUrl().isMatchingUrl(page.url, '/pricing')">
                        <Link href="/pricing" prefetch>
                        <Star />
                        <span>{{ t('Get Notenn Pro') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <SidebarMenuItem>
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child :is-active="useMatchUrl().isMatchingUrl(page.url, '/getstarted')">
                        <Link href="/getstarted" prefetch>
                        <MapPin />
                        <span>{{ t('Get Started') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child :is-active="useMatchUrl().isMatchingUrl(page.url, '/abc')">
                        <Link href="/abc" prefetch>
                        <NotepadText />
                        <span>{{ t('ABC Cheat Sheet') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child :is-active="useMatchUrl().isMatchingUrl(page.url, '/blog')">
                        <Link href="/blog" prefetch>
                        <Rss />
                        <span>{{ t('Blog') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                        as-child :is-active="useMatchUrl().isMatchingUrl(page.url, '/legal')">
                        <Link href="/legal" prefetch>
                        <BriefcaseBusiness />
                        <span>{{ t('Legal') }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
