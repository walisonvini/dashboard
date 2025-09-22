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

const submit = () => {
    if (!props.category) return;
    
    form.put(route('ticket-categories.update', props.category.id), {
        onSuccess: () => {
            emit('update:isOpen', false);
            form.reset();
        },
    });
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="(value) => emit('update:isOpen', value)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Edit Category</DialogTitle>
                <DialogDescription>
                    Update the category information below.
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Category Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Enter category name"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-sm text-red-500">
                        {{ form.errors.name }}
                    </p>

                    <Label for="description">Category Description</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Enter category description"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-red-500': form.errors.description }"
                    />
                    <p v-if="form.errors.description" class="text-sm text-red-500">
                        {{ form.errors.description }}
                    </p>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        Update Category
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template> 