<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Log } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Eye } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Logs',
        href: '/logs',
    },
];

defineProps<{
    logs: Log[];
}>();


</script>

<template>
    <Head title="Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Logs</h1>
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
                        <TableRow v-for="log in logs" :key="log.id">
                            <TableCell>{{ log.action }}</TableCell>
                            <TableCell>{{ log.model }} #{{ log.model_id }}</TableCell>
                            <TableCell>{{ log.user?.name ?? '—' }}</TableCell>
                            <TableCell>{{ log.ip_address ?? '—' }}</TableCell>
                            <TableCell>{{ new Date(log.created_at).toLocaleString() }}</TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <Link :href="route('logs.show', log.id)">
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
