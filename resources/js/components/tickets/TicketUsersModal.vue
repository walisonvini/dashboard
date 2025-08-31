<script setup lang="ts">
import axios from 'axios';
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/loading';
import { Ticket } from '@/types/ticket';
import { useToast } from '@/composables/useToast';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    ticket: Ticket;
    isOpen: boolean;
}

interface User {
    id: number;
    name: string;
    email: string;
}

const props = defineProps<Props>();
const { error: showError } = useToast();
const users = ref<User[]>([]);
const isLoading = ref(false);
const isModalReady = ref(false);
const searchQuery = ref('');
const filteredUsers = ref<User[]>([]);
const showDropdown = ref(false);

const form = useForm({
    name: '',
    role: 'observer',
    user_id: null as number | null,
});

const removeForm = useForm({});

const emit = defineEmits<{
    (e: 'update:isOpen', value: boolean): void;
}>();

watch(() => props.isOpen, async (newIsOpen, oldIsOpen) => {
    if (newIsOpen) {
        isLoading.value = true;
        isModalReady.value = false;
        
        try {
            const { data } = await axios.get(route('tickets.available-users', { ticket: props.ticket.id }));
            
            const usersData = data.users || [];
            users.value = usersData;
            filteredUsers.value = usersData;
            isModalReady.value = true;

        } catch (error) {
            showError('Error', 'Failed to fetch available users');
            emit('update:isOpen', false);
        } finally {
            isLoading.value = false;
        }
    }
})

const filterUsers = () => {
    if (!Array.isArray(users.value)) {
        filteredUsers.value = [];
        return;
    }
    
    if (!searchQuery.value.trim()) {
        filteredUsers.value = users.value;
        return;
    }
    
    filteredUsers.value = users.value.filter(user => 
        user.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.email?.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
};

const selectUser = (user: User) => {
    form.name = user.name;
    form.user_id = user.id;
    searchQuery.value = user.name;
    showDropdown.value = false;
};

const handleBlur = () => {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
};

const submit = () => {
    if (!form.user_id) {
        showError('Error', 'Please select a valid user');
        return;
    }

    form.post(route('tickets.users.store', { 
        ticket: props.ticket.id, 
        user: form.user_id 
    }), {
        onSuccess: () => {
            emit('update:isOpen', false);
            form.reset();
            searchQuery.value = '';
        }
    });
};

const removeUser = (userId: number) => {
    removeForm.delete(route('tickets.users.destroy', { 
        ticket: props.ticket.id,
        user: userId 
    }), {
        onSuccess: () => {
            if (props.ticket.users) {
                emit('update:isOpen', false);
            }
        }
    });
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="(value: boolean) => emit('update:isOpen', value)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Ticket Users</DialogTitle>
                <DialogDescription>
                    Add users to this ticket for collaboration and notifications.
                </DialogDescription>
            </DialogHeader>

            <div v-if="isLoading" class="flex items-center justify-center py-8">
                <Spinner size="lg" class="text-primary" />
            </div>

            <div v-else class="space-y-4">
                <div class="space-y-2">
                    <Label for="search">Search Users</Label>
                    <div class="relative">
                        <Input 
                            v-model="searchQuery" 
                            placeholder="Search by name or email"
                            @input="filterUsers"
                            @focus="showDropdown = true"
                            @blur="handleBlur"
                        />
                        
                        <div 
                            v-if="showDropdown && filteredUsers.length > 0" 
                            class="absolute z-10 w-full mt-1 bg-background border border-border rounded-md shadow-lg max-h-60 overflow-y-auto"
                        >
                            <div class="px-3 py-2 text-xs text-muted-foreground border-b border-border">
                                {{ filteredUsers.length }} user{{ filteredUsers.length !== 1 ? 's' : '' }} found
                            </div>
                            <div 
                                v-for="user in filteredUsers" 
                                :key="user.id"
                                class="px-4 py-2 cursor-pointer hover:bg-accent hover:text-accent-foreground"
                                @click="selectUser(user)"
                            >
                                <div class="font-medium">{{ user.name }}</div>
                                <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <Label for="role">Role</Label>
                    <select
                        id="role"
                        v-model="form.role"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="observer">Observer</option>
                        <option value="contributor">Contributor</option>
                    </select>
                </div>
                
                <!-- Current Users Section -->
                <div v-if="props.ticket.users && props.ticket.users.length > 0" class="space-y-3">
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium mb-3">Current Users</h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <div 
                                v-for="user in props.ticket.users" 
                                :key="user.id"
                                class="flex items-center justify-between p-3 border rounded-lg"
                            >
                                <div>
                                    <div class="font-medium">{{ user.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                                    <div class="text-xs text-muted-foreground capitalize">
                                        Role: {{ user.pivot?.role || 'unknown' }}
                                    </div>
                                </div>
                                <Button 
                                    v-if="user.pivot?.role === 'observer' || user.pivot?.role === 'contributor'"
                                    type="button" 
                                    variant="outline" 
                                    size="sm"
                                    @click="removeUser(user.id)"
                                    :disabled="removeForm.processing"
                                >
                                    Remove
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <DialogFooter>
                <Button type="button" variant="outline" @click="emit('update:isOpen', false)">
                    Cancel
                </Button>
                <Button type="button" variant="default" @click="submit" :disabled="form.processing || isLoading">
                    Add User
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 