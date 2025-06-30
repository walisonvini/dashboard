<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { router } from '@inertiajs/vue3'

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    deleted_at: string;
}

interface Props {
    isOpen: boolean;
    user: User | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:isOpen': [value: boolean];
}>();

const restoreUser = () => {
    if (props.user) {
        router.patch(route('users.restore', props.user.id));
        emit('update:isOpen', false);
    }
};

const closeModal = () => {
    emit('update:isOpen', false);
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="emit('update:isOpen', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Restore User</DialogTitle>
                <DialogDescription>
                    Are you sure you want to restore <strong>{{ user?.name }}</strong>? 
                    This will make the user active again and they will be able to log in.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="closeModal">
                    Cancel
                </Button>
                <Button @click="restoreUser">
                    Restore User
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 