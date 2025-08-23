<script setup lang="ts">
import { Arrangement, Tunebook } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { useI18n } from 'vue-i18n';
import AddToTunebookModal from './AddToTunebookModal.vue';
import { CircleUser, GitFork, Pen, Printer } from 'lucide-vue-next';
import PrintDialog from '../abcjs/PrintDialog.vue';
import { ref } from 'vue';
import DeleteArrangementModal from '../DeleteArrangementModal.vue';
import Tooltip from '@/components/ui/tooltip/Tooltip.vue';
import TooltipTrigger from '@/components/ui/tooltip/TooltipTrigger.vue';
import TooltipContent from '@/components/ui/tooltip/TooltipContent.vue';
const { t } = useI18n();
defineProps<{
    arrangement: Arrangement;
    tunebooks: Tunebook[];
    user_arrangement_for_this_tune?: Arrangement
}>();

const isPrintDialogOpen = ref(false)

const pageData = usePage();

</script>

<template>
    <div class="flex gap-1/2 sm:gap-1 lg:gap-4">

        <Tooltip v-if="arrangement.user?.id === pageData.props.auth.user?.id">
            <TooltipTrigger>
                <Link prefetch
                    :href="route('arrangements.edit', { arrangement: arrangement.id, tune: arrangement.tune.id })">
                <Button variant="ghost" size="sm">
                    <Pen />
                </Button>
                </Link>
            </TooltipTrigger>
            <TooltipContent side="right" align="center">
                {{ t('Edit') }}
            </TooltipContent>
        </Tooltip>
        <Tooltip v-if="!user_arrangement_for_this_tune && arrangement.user?.id != pageData.props.auth.user?.id">
            <TooltipTrigger>
                <Link :href="route('arrangements.fork', { arrangement: arrangement.id, tune: arrangement.tune.id })"
                    method="post">
                <Button variant="ghost" size="sm">
                    <GitFork />
                </Button>
                </Link>
            </TooltipTrigger>
            <TooltipContent side="right" align="center">
                {{ t('Fork') }}
            </TooltipContent>
        </Tooltip>
        <Tooltip v-if="user_arrangement_for_this_tune && arrangement.user?.id != pageData.props.auth.user?.id">
            <TooltipTrigger>
                <Link prefetch :href="route('arrangements.show', { arrangement: user_arrangement_for_this_tune.id })">
                <Button variant="ghost" size="sm">
                    <CircleUser />
                </Button>
                </Link>
            </TooltipTrigger>
            <TooltipContent side="right" align="center">
                {{ t('My arrangement for this Tune') }}
            </TooltipContent>
        </Tooltip>


        <Tooltip>
            <TooltipTrigger>
                <AddToTunebookModal :tunebooks="tunebooks" :arrangement-id="arrangement.id" />
            </TooltipTrigger>
            <TooltipContent side="right" align="center">
                {{ t('Add to Tunebook') }}
            </TooltipContent>
        </Tooltip>
        <Tooltip>
            <TooltipTrigger>
                <div @click="isPrintDialogOpen = true" v-if="pageData.props.auth.user?.is_pro">
                    <Button variant="ghost" size="sm">
                        <Printer />
                    </Button>
                </div>
            </TooltipTrigger>
            <TooltipContent side="right" align="center">
                {{ t('Print') }}
            </TooltipContent>
        </Tooltip>
        <PrintDialog v-model="isPrintDialogOpen" :arrangement="arrangement" />
        <Tooltip>
            <TooltipTrigger>
                <DeleteArrangementModal :arrangement="arrangement" />
            </TooltipTrigger>
            <TooltipContent side="right" align="center">
                {{ t('Delete') }}
            </TooltipContent>
        </Tooltip>

    </div>
</template>
