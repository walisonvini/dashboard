<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface User {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<{
    isOpen: boolean;
    user: User | null;
}>();

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void;
}>();

const form = useForm({});

const submit = () => {
    if (!props.user) return;
    
    form.delete(route('users.destroy', props.user.id), {
        onSuccess: () => {
            emit('update:isOpen', false);
        },
    });
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="(value) => emit('update:isOpen', value)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete User</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete the user "{{ user?.name }}"?
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                    Cancel
                </Button>
                <Button type="button" variant="destructive" @click="submit" :disabled="form.processing">
                    Delete User
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 