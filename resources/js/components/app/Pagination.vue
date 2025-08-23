<script setup lang="ts">
import { MeilisearchPaginatedResponse, PaginatedResponse } from '@/types';
import Pagination from '../ui/pagination/Pagination.vue';
import PaginationContent from '../ui/pagination/PaginationContent.vue';
import PaginationEllipsis from '../ui/pagination/PaginationEllipsis.vue';
import PaginationItem from '../ui/pagination/PaginationItem.vue';
import PaginationNext from '../ui/pagination/PaginationNext.vue';
import PaginationPrevious from '../ui/pagination/PaginationPrevious.vue';
import PaginationLast from '../ui/pagination/PaginationLast.vue';
import PaginationFirst from '../ui/pagination/PaginationFirst.vue';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';
import { useWindowSize } from '@vueuse/core';

const { width } = useWindowSize()

defineProps<{
    data: PaginatedResponse<any> | MeilisearchPaginatedResponse<any>;
}>();

const emit = defineEmits(['pageChange']);

const onPageChange = (page: number) => {
    emit('pageChange', page);
};
</script>
<template>
    <Pagination v-show="data.last_page > 1" v-slot="{ page }" :items-per-page="data.per_page" :total="data.total"
        :page="data.current_page" @update:page="onPageChange" :sibling-count="1" :showEdges="true">
        <PaginationContent v-slot="{ items }">
            <PaginationFirst>
                <ChevronsLeft />
            </PaginationFirst>
            <PaginationPrevious>
                <ChevronLeft />
            </PaginationPrevious>


            <template v-if="width > 500">
                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page">
                        {{ item.value }}
                    </PaginationItem>
                    <PaginationEllipsis v-else :key="item.type" :index="index" />
                </template>
            </template>

            <PaginationNext>
                <ChevronRight />
            </PaginationNext>
            <PaginationLast>
                <ChevronsRight />
            </PaginationLast>
        </PaginationContent>
    </Pagination>
</template>
