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

interface Role {
    id: number;
    name: string;
}

const props = defineProps<{
    isOpen: boolean;
    role: Role | null;
}>();

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void;
}>();

const form = useForm({});

const submit = () => {
    if (!props.role) return;
    
    form.delete(route('roles.destroy', props.role.id), {
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
                <DialogTitle>Delete Role</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete the role "{{ role?.name }}"? This action cannot be undone.
                    <br><br>
                    <span class="text-yellow-600 font-medium">Warning:</span> If any users are currently assigned to this role, they will be automatically assigned to the default "user" role.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                    Cancel
                </Button>
                <Button type="button" variant="destructive" @click="submit" :disabled="form.processing">
                    Delete Role
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 