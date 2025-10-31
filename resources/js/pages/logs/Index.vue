<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Log, PaginationData } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Eye, Search } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import { useTableWithPagination } from '@/composables/useTableWithPagination';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Logs',
        href: '/logs',
    },
];

const props = defineProps<{
    logs: Log[];
    pagination: PaginationData;
}>();

const {
    currentPage,
    totalItems,
    itemsPerPage,
    siblingCount,
    handlePageChange,
    filters,
    handleSearch,
    updateFilter,
    clearFilters
} = useTableWithPagination(props, 'logs.index', {
    additionalParams: {
        actor: '',
        action: '',
        model: '',
        model_id: '',
        ip: ''
    }
});


</script>

<template>
    <Head title="Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Logs</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                <div class="space-y-2">
                    <Label for="actor">Actor</Label>
                    <Input id="actor" v-model="filters.actor" placeholder="Actor name" @keydown.enter="handleSearch" />
                </div>

                <div class="space-y-2">
                    <Label for="action">Action</Label>
                    <Input id="action" v-model="filters.action" placeholder="e.g. created, updated" @keydown.enter="handleSearch" />
                </div>

                <div class="space-y-2">
                    <Label for="model">Model</Label>
                    <Input id="model" v-model="filters.model" placeholder="Model name" @keydown.enter="handleSearch" />
                </div>

                <div class="space-y-2">
                    <Label for="model_id">Model ID</Label>
                    <Input id="model_id" v-model="filters.model_id" placeholder="Model ID" @keydown.enter="handleSearch" />
                </div>

                <div class="space-y-2">
                    <Label for="ip">IP</Label>
                    <Input id="ip" v-model="filters.ip" placeholder="IP address" @keydown.enter="handleSearch" />
                </div>

                <div class="space-y-2 flex items-end">
                    <Button @click="handleSearch" size="sm" variant="outline" class="h-9 px-3 w-full">
                        <Search class="h-4 w-4" />
                    </Button>
                </div>
               
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Action</TableHead>
                            <TableHead>Model</TableHead>
                            <TableHead>Actor</TableHead>
                            <TableHead>IP</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(log, index) in logs" :key="log?.id ?? index">
                                <TableCell>{{ log?.action ?? '—' }}</TableCell>
                                <TableCell>{{ log?.model ?? '—' }}{{ log?.model_id ? ' #' + log.model_id : '' }}</TableCell>
                                <TableCell>{{ log?.user?.name ?? '—' }}</TableCell>
                                <TableCell>{{ log?.ip_address ?? '—' }}</TableCell>
                                <TableCell>{{ log?.created_at ? new Date(log.created_at).toLocaleString() : '—' }}</TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <Link v-if="log?.id" :href="route('logs.show', log.id)">
                                            <Button variant="ghost" size="icon">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button v-else variant="ghost" size="icon" disabled>
                                            <Eye class="h-4 w-4" />
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
    </AppLayout>
</template>
