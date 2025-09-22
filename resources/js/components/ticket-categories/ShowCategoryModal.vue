<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { watch } from 'vue';
import { type TicketCategory } from '@/types/ticket';

const props = defineProps<{
    isOpen: boolean;
    category: TicketCategory | null;
}>();

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void;
}>();

const form = useForm({
    name: '',
    description: '',
});

watch(() => props.category, (newCategory) => {
    if (newCategory) {
        form.name = newCategory.name;
        form.description = newCategory.description;
    }
}, { immediate: true });

</script>

<template>
    <Dialog :open="isOpen" @update:open="(value) => emit('update:isOpen', value)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Show Category</DialogTitle>
                <DialogDescription>
                    View the category information below.
                </DialogDescription>
            </DialogHeader>
            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Category Name</Label>
                    <Input
                        disabled
                        id="name"
                        v-model="form.name"
                        placeholder="Enter category name"
                    />

                    <Label for="description">Category Description</Label>
                    <textarea
                        disabled
                        id="description"
                        v-model="form.description"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    />
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                        Exit
                    </Button>
                </DialogFooter>
            </div>
        </DialogContent>
    </Dialog>
</template> 