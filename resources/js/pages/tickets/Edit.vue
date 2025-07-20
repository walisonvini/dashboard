<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import TicketAttachments from '@/components/tickets/TicketAttachments.vue';
import TicketChat from '@/components/tickets/TicketChat.vue';
import TicketActions from '@/components/tickets/TicketActions.vue';
import { Ticket, TicketCategory } from '@/types/ticket';

const props = defineProps<{
  ticket: Ticket;
  categories: TicketCategory[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
    {
        title: 'Ticket',
        href: `/tickets/${props.ticket.id}`,
    },
];

// Mock data for visual demonstration
const mockAttachments = [
    { id: 1, ticket_id: 1, file_name: 'screenshot.png', original_name: 'screenshot.png', uploaded_by: 1, created_at: '2024-01-15 10:30' },
    { id: 2, ticket_id: 1, file_name: 'error_log.txt', original_name: 'error_log.txt', uploaded_by: 1, created_at: '2024-01-15 09:45' },
];


</script>

<template>
  <Head :title="`Ticket #${props.ticket.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col xl:flex-row gap-4 p-4">
            <!-- Left Column - Attachments -->
      <div class="w-full xl:w-80 flex-shrink-0 order-1 xl:order-1">
        <TicketAttachments :ticket="props.ticket" :attachments="props.ticket.attachments || []" />
      </div>

            <!-- Center Column - Chat -->
      <div class="flex-1 order-2 xl:order-2 min-h-0">
        <TicketChat :ticket="props.ticket" :comments="props.ticket.comments || []" />
      </div>

                  <!-- Right Column - Actions -->
      <div class="w-full xl:w-80 flex-shrink-0 order-3 xl:order-3">
        <TicketActions :ticket="props.ticket" :categories="props.categories" />
      </div>
    </div>
  </AppLayout>
</template>