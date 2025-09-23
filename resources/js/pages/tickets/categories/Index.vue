<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Edit, Trash2, Eye } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { type TicketCategory } from '@/types/ticket';
import CreateCategoryModal from '@/components/ticket-categories/CreateCategoryModal.vue';
import EditCategoryModal from '@/components/ticket-categories/EditCategoryModal.vue';
import ShowCategoryModal from '@/components/ticket-categories/ShowCategoryModal.vue';
import DeleteOrDeactivateCategoryModal from '@/components/ticket-categories/DeleteOrDeactivateCategoryModal.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { PaginationData } from '@/types';
import { useTableWithPagination } from '@/composables/useTableWithPagination';
import { Search } from 'lucide-vue-next';

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
} = useTableWithPagination(props, 'ticket-categories.index');

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isShowModalOpen = ref(false);
const isDeleteOrDeactivateModalOpen = ref(false);
const selectedCategory = ref<TicketCategory | null>(null);

const openDeleteOrDeactivateModal = (category: TicketCategory) => {
    selectedCategory.value = category;
    isDeleteOrDeactivateModalOpen.value = true;
};

const openShowModal = (category: TicketCategory) => {
    selectedCategory.value = category;
    isShowModalOpen.value = true;
};

const openEditModal = (category: TicketCategory) => {
    selectedCategory.value = category;
    isEditModalOpen.value = true;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
    {
        title: 'Categories',
        href: '/tickets/categories',
    },
];
</script>

<template>
    <Head title="Ticket Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Ticket Categories</h1>

                <div class="flex items-center gap-2">
                    <Link :href="route('ticket-categories.deactivated')">
                        <Button variant="outline">
                            Deactivated Categories
                        </Button>
                    </Link>
                    <CreateCategoryModal v-model:isOpen="isCreateModalOpen" />
                </div>
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
                            <TableHead class="w-1/3">Name</TableHead>
                            <TableHead class="w-1/2">Description</TableHead>
                            <TableHead class="w-1/6">Created</TableHead>
                            <TableHead class="w-20">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="category in categories" :key="category.id">
                            <TableCell class="max-w-xs">
                                <div class="truncate font-medium" :title="category.name">{{ category.name }}</div>
                            </TableCell>
                            <TableCell class="max-w-md">
                                <div class="truncate text-muted-foreground" :title="category.description">
                                    {{ category.description ? (category.description.length > 50 ? category.description.substring(0, 50) + '...' : category.description) : 'No description' }}
                                </div>
                            </TableCell>
                            <TableCell>{{ new Date(category.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button variant="ghost" size="icon" @click="openShowModal(category)">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon" @click="openEditModal(category)">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon" @click="openDeleteOrDeactivateModal(category)">
                                        <Trash2 class="h-4 w-4" />
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

        <EditCategoryModal v-model:isOpen="isEditModalOpen" :category="selectedCategory" />
        <ShowCategoryModal v-model:isOpen="isShowModalOpen" :category="selectedCategory" />
        <DeleteOrDeactivateCategoryModal v-model:isOpen="isDeleteOrDeactivateModalOpen" :category="selectedCategory" />
    </AppLayout>
</template>
