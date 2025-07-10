<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
];

interface Ticket {
    id: number;
    title: string;
    description: string;
    status: string;
    priority: string;
    category: {
        id: number;
        name: string;
    };
    created_at: string;
}

const props = defineProps<{
    tickets: Ticket[]
}>();
</script>

<template>
    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tickets</h1>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Priority</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="ticket in tickets" :key="ticket.id">
                            <TableCell>{{ ticket.title }}</TableCell>
                            <TableCell>{{ ticket.category.name }}</TableCell>
                            <TableCell>
                                <Badge :variant="ticket.status === 'open' ? 'open' : ticket.status === 'in_progress' ? 'in_progress' : 'closed'">
                                    {{ ticket.status }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Badge :variant="ticket.priority">
                                    {{ ticket.priority }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ new Date(ticket.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="route('tickets.edit', ticket.id)">
                                        <Button variant="ghost" size="icon">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button 
                                        variant="ghost" 
                                        size="icon"
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
    </AppLayout>
</template>
