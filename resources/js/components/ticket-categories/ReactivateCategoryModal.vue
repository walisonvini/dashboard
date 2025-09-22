<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useToast } from '@/composables/useToast'

interface TicketCategory {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    isOpen: boolean;
    category: TicketCategory | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:isOpen': [value: boolean];
}>();

const isProcessing = ref(false);
const { error: showError } = useToast();

const reactivateCategory = () => {
    if (!props.category) return;
    
    isProcessing.value = true;
    router.put(route('ticket-categories.reactivate', props.category.id), {}, {
        onSuccess: () => {
            emit('update:isOpen', false);
            isProcessing.value = false;
        },
        onError: (errors) => {
            isProcessing.value = false;
            if (errors.message) {
                showError('Error', errors.message);
            }
        },
    });
};

const closeModal = () => {
    emit('update:isOpen', false);
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="emit('update:isOpen', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Reactivate Category</DialogTitle>
                <DialogDescription>
                    Are you sure you want to reactivate <strong>{{ category?.name }}</strong>? 
                    This will make the category available for use in new tickets.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="closeModal">
                    Cancel
                </Button>
                <Button @click="reactivateCategory" :disabled="isProcessing">
                    Reactivate Category
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
