<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { RotateCcw } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ref } from 'vue';
import RestoreUserModal from '@/components/users/RestoreUserModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users'
    },
    {
        title: 'Trashed',
        href: '/users/trashed'
    }
];

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    deleted_at: string;
}

const props = defineProps<{
    users: User[]
}>();

const isRestoreModalOpen = ref(false);
const selectedUser = ref<User | null>(null);

const openRestoreModal = (user: User) => {
    selectedUser.value = user;
    isRestoreModalOpen.value = true;
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Users</h1>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Deleted At</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="users.length === 0">
                            <TableCell colspan="5" class="text-center py-8 text-muted-foreground">
                                No trashed users found.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="user in users" :key="user.id">
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>{{ new Date(user.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>{{ new Date(user.deleted_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Button 
                                        variant="ghost" 
                                        size="icon"
                                        @click="openRestoreModal(user)"
                                    >
                                        <RotateCcw class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <RestoreUserModal
            v-model:isOpen="isRestoreModalOpen"
            :user="selectedUser"
        />
    </AppLayout>
</template>
