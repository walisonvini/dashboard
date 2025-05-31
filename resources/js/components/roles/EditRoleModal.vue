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

const form = useForm({
    name: '',
});

watch(() => props.role, (newRole) => {
    if (newRole) {
        form.name = newRole.name;
    }
}, { immediate: true });

const submit = () => {
    if (!props.role) return;
    
    form.put(route('roles.update', props.role.id), {
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
                <DialogTitle>Edit Role</DialogTitle>
                <DialogDescription>
                    Update the role information below.
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Role Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Enter role name"
                        :class="{ 'border-red-500': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-sm text-red-500">
                        {{ form.errors.name }}
                    </p>
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        Update Role
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template> 