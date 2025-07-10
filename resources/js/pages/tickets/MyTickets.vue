<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Pencil, Eye } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Tickets',
        href: '/tickets/my-tickets',
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
    pivot?: {
        role: string;
    };
}

const props = defineProps<{
    tickets: Ticket[]
}>();
</script>

<template>
    <Head title="My Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">My Tickets</h1>
                <Link :href="route('tickets.create')">
                    <Button>Create Ticket</Button>
                </Link>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Priority</TableHead>
                            <TableHead>Role</TableHead>
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
                            <TableCell>{{ ticket.pivot?.role || 'N/A' }}</TableCell>
                            <TableCell>{{ new Date(ticket.created_at).toLocaleDateString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="route('tickets.show', ticket.id)">
                                        <Button variant="ghost" size="icon">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
