<script setup lang="ts">
import { ref } from 'vue';
import { type TicketCategory } from '@/types/ticket';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    isOpen: boolean;
    category: TicketCategory | null;
}>();

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void;
}>();

const isProcessing = ref(false);
const { error: showError } = useToast();

const deleteCategory = () => {
    if (!props.category) return;
    
    isProcessing.value = true;
    router.delete(route('ticket-categories.destroy', props.category.id), {
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

const deactivateCategory = () => {
    if (!props.category) return;
    
    isProcessing.value = true;
    router.put(route('ticket-categories.deactivate', props.category.id), {}, {
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
</script>

<template>
    <Dialog :open="isOpen" @update:open="(value) => emit('update:isOpen', value)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete or Deactivate Category</DialogTitle>
                <DialogDescription>
                    You can choose between two options for the category "{{ category?.name }}":
                    <br><br>
                    <strong>Deactivate:</strong> The category will be deactivated but will remain in the system. You can reactivate it later.
                    <br><br>
                    <strong>Delete:</strong> The category will be permanently removed from the system. <span class="text-red-600 font-semibold">This action is irreversible!</span>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                    Cancel
                </Button>
                <Button type="button" variant="destructive" @click="deleteCategory" :disabled="isProcessing">
                    Delete Category
                </Button>
                <Button type="button" variant="secondary" @click="deactivateCategory" :disabled="isProcessing">
                    Deactivate Category
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 