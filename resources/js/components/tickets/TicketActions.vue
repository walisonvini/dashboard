<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Ticket, TicketCategory } from '@/types/ticket';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    ticket: Ticket;
    categories: TicketCategory[];
    isSupport: boolean;
}

const props = defineProps<Props>();
const page = usePage();

const form = useForm({
    priority: props.ticket.priority,
    category: props.ticket.category.id,
    status: props.ticket.status,
});

const isTicketOpen = computed(() => props.ticket.status === 'open');

const saveChanges = () => {
    form.put(route('tickets.update', props.ticket.id));
};

const assignTicket = () => {
    form.post(route('tickets.assign', props.ticket.id));
};

const unassignTicket = () => {
    form.post(route('tickets.unassign', props.ticket.id));
};
</script>

<template>
    <Card class="h-full flex flex-col">
        <CardHeader>
            <CardTitle class="text-lg">Actions</CardTitle>
        </CardHeader>
        <CardContent class="flex-1 flex flex-col space-y-3 md:space-y-4">
            <!-- Status -->
            <div class="space-y-2">
                <Label>Status</Label>
                <select 
                    v-model="form.status"
                    :disabled="!isSupport"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="waiting_user">Waiting User</option>
                    <option value="waiting_third_party">Waiting Third Party</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>

            <!-- Priority -->
            <div class="space-y-2">
                <Label>Priority</Label>
                <select 
                    v-model="form.priority"
                    :disabled="!isTicketOpen && !isSupport"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <!-- Category -->
            <div class="space-y-2">
                <Label>Category</Label>
                <select 
                    v-model="form.category"
                    :disabled="!isTicketOpen && !isSupport"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                </select>
            </div>

            <Separator />

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-1 gap-2">
                <Button @click="saveChanges" class="w-full" :disabled="!isTicketOpen && !isSupport">Save Changes</Button>

                <Button 
                    v-if="isSupport && !ticket.users?.some(user => user.id === (page.props.auth as any).user.id && user.pivot.role === 'assigned')" 
                    @click="assignTicket"
                    variant="outline"
                    class="w-full"
                >
                    Assign Ticket
                </Button>

                <Button
                    v-if="isSupport && ticket.users?.some(user => user.id === (page.props.auth as any).user.id && user.pivot.role === 'assigned')" 
                    @click="unassignTicket" 
                    variant="outline"
                    class="w-full"
                >
                    Unassign Ticket
                </Button>
            </div>

            <Separator />

            <!-- Ticket Information -->
            <div class="space-y-3">
                <h4 class="font-medium">Information</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Created at:</span>
                        <span>{{ new Date(ticket.created_at).toLocaleDateString() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Updated at:</span>
                        <span>{{ new Date(ticket.updated_at).toLocaleDateString() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Requester:</span>
                        <span>{{ ticket.users?.filter(user => user.pivot.role === 'requester').map(user => user.name).join(', ') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Assigned to:</span>
                        <span>{{ ticket.users?.filter(user => user.pivot.role === 'assigned').map(user => user.name).join(', ') || 'None' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Contributors:</span>
                        <span>{{ ticket.users?.filter(user => user.pivot.role === 'contributor').map(user => user.name).join(', ') || 'None' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Observers:</span>
                        <span>{{ ticket.users?.filter(user => user.pivot.role === 'observer').map(user => user.name).join(', ') || 'None' }}</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 