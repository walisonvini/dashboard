<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';

interface User {
    id: number;
    name: string;
    email: string;
    roles: string[];
}

interface Role {
    id: number;
    name: string;
}

const props = defineProps<{
    user: User;
    roles: Role[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
    {
        title: 'Edit',
        href: `/users/${props.user.id}/edit`,
    },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    roles: props.user.roles,
});

const handleRoleChange = (roleName: string, checked: boolean) => {
    if (checked) {
        if (!form.roles.includes(roleName)) {
            form.roles.push(roleName);
        }
    } else {
        const index = form.roles.indexOf(roleName);
        if (index > -1) {
            form.roles.splice(index, 1);
        }
    }
};

const submit = () => {
    form.put(route('users.update', props.user.id), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Edit User</h1>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>User Information</CardTitle>
                </CardHeader>
                <form @submit.prevent="submit">
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter user name"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-500">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="Enter user email"
                                :class="{ 'border-red-500': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="text-sm text-red-500">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="password">New Password</Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="Enter new password (optional)"
                                :class="{ 'border-red-500': form.errors.password }"
                            />
                            <p v-if="form.errors.password" class="text-sm text-red-500">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm New Password</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Confirm new password"
                            />
                        </div>

                        <div class="space-y-4">
                            <Label>Roles</Label>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                                <div v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
                                    <Checkbox
                                        :id="'role-' + role.id"
                                        :model-value="form.roles.includes(role.name)"
                                        @update:modelValue="(checked) => handleRoleChange(role.name, Boolean(checked))"
                                    />
                                    <Label :for="'role-' + role.id" class="text-sm font-normal">
                                        {{ role.name }}
                                    </Label>
                                </div>
                            </div>
                            <p v-if="form.errors.roles" class="text-sm text-red-500">
                                {{ form.errors.roles }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter class="mt-4 flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.visit(route('users.index'))"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            Update User
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
