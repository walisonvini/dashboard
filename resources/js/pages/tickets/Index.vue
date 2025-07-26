<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Eye } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useTicketFormatting } from '@/composables/useTicketFormatting';

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
    updated_at: string;
}

defineProps<{
    tickets: Ticket[]
}>();

const { formatStatus, formatPriority, getStatusVariant } = useTicketFormatting();

</script>

<template>
    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tickets</h1>

                <Link :href="route('tickets.create')">
                    <Button>New Ticket</Button>
                </Link>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-1/12">Incident ID</TableHead>
                            <TableHead class="w-1/3">Title</TableHead>
                            <TableHead class="w-1/6">Category</TableHead>
                            <TableHead class="w-1/6 text-center">Status</TableHead>
                            <TableHead class="w-1/6 text-center">Priority</TableHead>
                            <TableHead class="w-1/6">Last Updated</TableHead>
                            <TableHead class="w-20">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="ticket in tickets" :key="ticket.id">
                            <TableCell>#{{ ticket.id }}</TableCell>
                            <TableCell class="max-w-xs">
                                <div class="truncate" :title="ticket.title">{{ ticket.title }}</div>
                            </TableCell>
                            <TableCell>{{ ticket.category.name }}</TableCell>
                            <TableCell class="text-center">
                                <Badge :variant="getStatusVariant(ticket.status) as any">
                                    {{ formatStatus(ticket.status) }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-center">
                                <Badge :variant="ticket.priority === 'low' ? 'low' : ticket.priority === 'medium' ? 'medium' : 'high'">
                                    {{ formatPriority(ticket.priority) }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ new Date(ticket.updated_at).toLocaleString('pt-BR') }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="route('tickets.edit', ticket.id)">
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
