import { computed } from 'vue';
import { Ticket } from '@/types/ticket';

export function useTicketStatus(ticket: Ticket) {
    const isTicketClosed = computed(() => ticket.status === 'closed');
    const isTicketCanceled = computed(() => ticket.status === 'canceled');
    const isTicketClosedOrCanceled = computed(() => isTicketClosed.value || isTicketCanceled.value);

    return {
        isTicketClosed,
        isTicketCanceled,
        isTicketClosedOrCanceled
    };
} 