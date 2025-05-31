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

const props = defineProps<{
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
        <DialogTrigger asChild>
            <Button>Add Role</Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Create New Role</DialogTitle>
                <DialogDescription>
                    Add a new role to the system. Fill in the details below.
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
                        Create Role
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template> 