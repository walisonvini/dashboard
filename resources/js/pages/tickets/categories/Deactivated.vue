<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { RotateCcw } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';
import ReactivateCategoryModal from '@/components/ticket-categories/ReactivateCategoryModal.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { PaginationData } from '@/types';
import { useTableWithPagination } from '@/composables/useTableWithPagination';
import { Search } from 'lucide-vue-next';
import { type TicketCategory } from '@/types/ticket';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets'
    },
    {
        title: 'Categories',
        href: '/ticket-categories'
    },
    {
        title: 'Deactivated',
        href: '/ticket-categories/deactivated'
    }
];

const props = defineProps<{
    categories: TicketCategory[];
    pagination: PaginationData;
}>();

const {
    currentPage,
    totalItems,
    itemsPerPage,
    siblingCount,
    handlePageChange,
    searchQuery,
    handleSearch
} = useTableWithPagination(props, 'ticket-categories.deactivated');

const isReactivateModalOpen = ref(false);
const selectedCategory = ref<TicketCategory | null>(null);

const openReactivateModal = (category: TicketCategory) => {
    selectedCategory.value = category;
    isReactivateModalOpen.value = true;
};
</script>

<template>
    <Head title="Deactivated Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Deactivated Categories</h1>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search by name or description..."
                        class="pl-10"
                        @keydown.enter="handleSearch"
                    />
                </div>
                <Button 
                    @click="handleSearch"
                    size="sm"
                    variant="outline"
                    class="h-9 px-3"
                >
                    <Search class="h-4 w-4" />
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Updated At</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="categories.length === 0">
                            <TableCell colspan="5" class="text-center py-8 text-muted-foreground">
                                No deactivated categories found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="category in categories" :key="category.id">
                            <TableCell>{{ category.name }}</TableCell>
                            <TableCell class="max-w-md">
                                <div class="truncate text-muted-foreground" :title="category.description">
                                    {{ category.description ? (category.description.length > 50 ? category.description.substring(0, 50) + '...' : category.description) : 'No description' }}
                                </div>
                            </TableCell>
                            <TableCell>{{ new Date(category.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>{{ new Date(category.updated_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button 
                                        variant="ghost" 
                                        size="icon"
                                        @click="openReactivateModal(category)"
                                    >
                                        <RotateCcw class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-6">
                <Pagination
                    v-model:current-page="currentPage"
                    :total-items="totalItems"
                    :items-per-page="itemsPerPage"
                    :show-edges="true"
                    :sibling-count="siblingCount"
                    @update:current-page="handlePageChange"
                />
            </div>
        </div>

        <ReactivateCategoryModal
            v-model:isOpen="isReactivateModalOpen"
            :category="selectedCategory"
        />
    </AppLayout>
</template>
