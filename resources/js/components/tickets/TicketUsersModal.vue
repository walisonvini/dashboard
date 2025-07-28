<script setup lang="ts">
import { ref } from 'vue';
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
    DialogTrigger,
} from '@/components/ui/dialog';

defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void;
}>();

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('roles.store'), {
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
                <DialogTitle>Ticket Users</DialogTitle>
            </DialogHeader>
            <div class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Name</Label>
                    <Input v-model="form.name" placeholder="Enter user name" />
                </div>
            </div>
            <DialogFooter>
                <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                    Cancel
                </Button>
                <Button type="button" variant="default" @click="submit" :disabled="form.processing">
                    Add User
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 