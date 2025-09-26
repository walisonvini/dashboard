<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, PaginationData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Eye } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useTicketFormatting } from '@/composables/useTicketFormatting';
import { type Ticket, type TicketCategory } from '@/types/ticket';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { useTableWithPagination } from '@/composables/useTableWithPagination';
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
];

const props = defineProps<{
    tickets: Ticket[];
    pagination: PaginationData;
    categories: TicketCategory[];
    isSupport: boolean;
}>();

console.log(props.tickets);

const { formatStatus, formatPriority, getStatusVariant } = useTicketFormatting();

const {
    currentPage,
    totalItems,
    itemsPerPage,
    siblingCount,
    handlePageChange,
    searchQuery,
    handleSearch,
    filters,
    updateFilter,
} = useTableWithPagination(props, 'tickets.index', {
    additionalParams: {
        category: '',
        status: '',
        priority: '',
        assignment: ''
    }
});

const categoryFilter = computed({
    get: () => filters.category || '',
    set: (value) => updateFilter('category', value)
});

const statusFilter = computed({
    get: () => filters.status || '',
    set: (value) => updateFilter('status', value)
});

const priorityFilter = computed({
    get: () => filters.priority || '',
    set: (value) => updateFilter('priority', value)
});

const assignmentFilter = computed({
    get: () => filters.assignment || '',
    set: (value) => updateFilter('assignment', value)
});

const handleFilterChange = () => {
    handleSearch();
};

const clearFilters = () => {
    router.visit(route('tickets.index'));
};

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

            <div class="flex items-center gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Search by code or title..."
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4" :class="{ 'lg:grid-cols-5': isSupport }">
                <div class="space-y-2">
                    <Label for="category-filter">Category</Label>
                    <select
                        id="category-filter"
                        v-model="categoryFilter"
                        @change="handleFilterChange"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Categories</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <div class="space-y-2">
                    <Label for="status-filter">Status</Label>
                    <select
                        id="status-filter"
                        v-model="statusFilter"
                        @change="handleFilterChange"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <Label for="priority-filter">Priority</Label>
                    <select
                        id="priority-filter"
                        v-model="priorityFilter"
                        @change="handleFilterChange"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div v-if="isSupport" class="space-y-2">
                    <Label for="involvement-filter">Involvement</Label>
                    <select
                        id="involvement-filter"
                        v-model="assignmentFilter"
                        @change="handleFilterChange"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="">All Tickets</option>
                        <option value="involved">Involved</option>
                        <option value="not_involved">Not Involved</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <Label>&nbsp;</Label>
                    <Button 
                        @click="clearFilters"
                        variant="outline"
                        class="w-full h-10"
                    >
                        Clear Filters
                    </Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-1/12">Code</TableHead>
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
                            <TableCell>{{ ticket.code }}</TableCell>
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
    </AppLayout>
</template>
