<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { ref } from 'vue';
import { type BreadcrumbItem } from '@/types';

interface Ticket {
    id: number;
    title: number;
    description: string;
    status: string;
    priority: string;
    category: {
        id: number;
        name: string;
    };
}

const props = defineProps<{
  ticket: Ticket;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
    {
        title: 'View',
        href: `/tickets/${props.ticket.id}`,
    },
];

</script>

<template>
  <Head :title="`Ticket #${props.ticket.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <h1 class="text-2xl font-bold">Ticket #{{ props.ticket.id }}</h1>
    </div>

  </AppLayout>
</template>