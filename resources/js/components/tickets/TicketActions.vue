<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Ticket, TicketCategory } from '@/types/ticket';

interface Props {
    ticket: Ticket;
    categories: TicketCategory[];
}

const props = defineProps<Props>();
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
                <div class="flex h-10 w-full items-center rounded-md border border-input bg-background px-3 py-2 text-sm">
                    <span class="capitalize">{{ ticket.status }}</span>
                </div>
            </div>

            <!-- Priority -->
            <div class="space-y-2">
                <Label>Priority</Label>
                <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <!-- Category -->
            <div class="space-y-2">
                <Label>Category</Label>
                <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                    <option :value="ticket.category.id">{{ ticket.category.name }}</option>
                </select>
            </div>

            <Separator />

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-3 xl:grid-cols-1 gap-2">
                <Button class="w-full">Save Changes</Button>
                <Button variant="outline" class="w-full">Assign Ticket</Button>
                <Button variant="outline" class="w-full">Close Ticket</Button>
            </div>

            <Separator />

            <!-- Ticket Information -->
            <div class="space-y-3">
                <h4 class="font-medium">Information</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Created at:</span>
                        <span>01/01/2024</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Updated at:</span>
                        <span>01/01/2024</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Assigned to:</span>
                        <span>Unassigned</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 