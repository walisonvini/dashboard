<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import CreateRoleModal from '@/components/roles/CreateRoleModal.vue';
import EditRoleModal from '@/components/roles/EditRoleModal.vue';
import DeleteRoleModal from '@/components/roles/DeleteRoleModal.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Edit, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { PaginationData } from '@/types';
import { useTableWithPagination } from '@/composables/useTableWithPagination';
import { Search } from 'lucide-vue-next';

interface Role {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

const props = defineProps<{
    roles: Role[];
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
} = useTableWithPagination(props, 'roles.index');

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedRole = ref<Role | null>(null);

const openEditModal = (role: Role) => {
    selectedRole.value = role;
    isEditModalOpen.value = true;
};

const openDeleteModal = (role: Role) => {
    selectedRole.value = role;
    isDeleteModalOpen.value = true;
};
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Roles</h1>
                <CreateRoleModal v-model:isOpen="isCreateModalOpen" />
            </div>

            <!-- Search Input -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search by role name..."
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
                            <TableHead>Created At</TableHead>
                            <TableHead>Updated At</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="role in roles" :key="role.id">
                            <TableCell>{{ role.name }}</TableCell>
                            <TableCell>{{ new Date(role.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>{{ new Date(role.updated_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="openEditModal(role)"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="openDeleteModal(role)"
                                    >
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

        <EditRoleModal
            v-model:isOpen="isEditModalOpen"
            :role="selectedRole"
        />

        <DeleteRoleModal
            v-model:isOpen="isDeleteModalOpen"
            :role="selectedRole"
        />
    </AppLayout>
</template>
