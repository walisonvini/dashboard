<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Edit, Plus, Trash2, Eye } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type TicketCategory } from '@/types/ticket';

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

defineProps<{
    categories: TicketCategory[]
}>();
</script>

<template>
    <Head title="Ticket Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Ticket Categories</h1>

                <Button>
                    Add Category
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
                                    <Button variant="ghost" size="icon">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
