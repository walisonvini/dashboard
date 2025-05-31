<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ref } from 'vue';
import CreateRoleModal from '@/components/roles/CreateRoleModal.vue';
import EditRoleModal from '@/components/roles/EditRoleModal.vue';
import DeleteRoleModal from '@/components/roles/DeleteRoleModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];

interface Role {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    roles: Role[]
}>();

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
                                        <Pencil class="h-4 w-4" />
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
