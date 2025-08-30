<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import TicketAttachments from '@/components/tickets/TicketAttachments.vue';
import TicketChat from '@/components/tickets/TicketChat.vue';
import TicketActions from '@/components/tickets/TicketActions.vue';
import { type Ticket, type TicketCategory } from '@/types/ticket';

const props = defineProps<{
  ticket: Ticket;
  categories: TicketCategory[];
  isSupport: boolean;
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
        <TicketActions :ticket="props.ticket" :categories="props.categories" :is-support="props.isSupport" />
      </div>
    </div>
  </AppLayout>
</template>