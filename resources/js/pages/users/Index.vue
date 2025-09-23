<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DeleteUserModal from '@/components/users/DeleteUserModal.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Edit, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';
import { PaginationData, User } from '@/types';
import { Search } from 'lucide-vue-next';
import { useTableWithPagination } from '@/composables/useTableWithPagination';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

const props = defineProps<{
    users: User[];
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
} = useTableWithPagination(props, 'users.index');

const isDeleteModalOpen = ref(false);
const selectedUser = ref<User | null>(null);

const openDeleteModal = (user: User) => {
    selectedUser.value = user;
    isDeleteModalOpen.value = true;
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Users</h1>
                <div class="flex items-center gap-2">
                    <Link :href="route('users.trashed')">
                        <Button variant="outline">Trashed Users</Button>
                    </Link>
                    <Link :href="route('users.create')">
                        <Button>Add User</Button>
                    </Link>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search by name or email..."
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
                            <TableHead>Email</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users" :key="user.id">
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>{{ new Date(user.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="route('users.edit', user.id)">
                                        <Button variant="ghost" size="icon">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button 
                                        variant="ghost" 
                                        size="icon"
                                        @click="openDeleteModal(user)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

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

        <DeleteUserModal
            v-model:isOpen="isDeleteModalOpen"
            :user="selectedUser"
        />
    </AppLayout>
</template>
