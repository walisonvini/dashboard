<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ref } from 'vue';
import DeleteUserModal from '@/components/users/DeleteUserModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

const props = defineProps<{
    users: User[]
}>();

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
                                            <Pencil class="h-4 w-4" />
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
        </div>

        <DeleteUserModal
            v-model:isOpen="isDeleteModalOpen"
            :user="selectedUser"
        />
    </AppLayout>
</template>
