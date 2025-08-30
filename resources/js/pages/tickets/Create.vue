<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'My Tickets',
        href: '/my-tickets',
    },
    {
        title: 'Create Ticket',
        href: '/tickets/create',
    },
];

interface TicketCategory {
    id: number;
    name: string;
}

const props = defineProps<{
    categories: TicketCategory[];
}>();

const form = useForm({
    title: '',
    description: '',
    category_id: '',
    priority: 'medium',
    initial_comment: '',
    attachments: [] as File[],
});

const handleAttachments = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        form.attachments = Array.from(target.files);
    }
};

const submit = () => {
    form.post(route('tickets.store'), {
        forceFormData: true
    });
};
</script>

<template>
    <Head title="Create Ticket" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Create Ticket</h1>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>New Ticket</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="title">Title</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="Enter ticket title"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="text-sm text-red-500">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="category">Category</Label>
                            <select
                                id="category"
                                v-model="form.category_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.category_id }"
                            >
                                <option value="">Select a category</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="text-sm text-red-500">
                                {{ form.errors.category_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="priority">Priority</Label>
                            <select
                                id="priority"
                                v-model="form.priority"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.priority }"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <p v-if="form.errors.priority" class="text-sm text-red-500">
                                {{ form.errors.priority }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description (Optional)</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Describe your issue or request"
                                rows="6"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.description }"
                            />
                            <p v-if="form.errors.description" class="text-sm text-red-500">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="initial_comment">Initial Comment (Optional)</Label>
                            <textarea
                                id="initial_comment"
                                v-model="form.initial_comment"
                                placeholder="Add any additional details or context"
                                rows="4"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :class="{ 'border-red-500': form.errors.initial_comment }"
                            />
                            <p v-if="form.errors.initial_comment" class="text-sm text-red-500">
                                {{ form.errors.initial_comment }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="attachments">Attachments</Label>
                            <input
                                id="attachments"
                                type="file"
                                multiple
                                @change="handleAttachments"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground file:shadow-xs file:hover:bg-primary/90"
                            />
                            <div v-if="form.attachments.length" class="mt-1 flex flex-wrap gap-2">
                                <span v-for="file in form.attachments" :key="file.name" class="inline-block bg-primary-100 rounded px-2 py-1 text-xs text-white">
                                    {{ file.name }}
                                </span>
                            </div>
                            <p v-if="form.errors.attachments" class="text-sm text-red-500">
                                {{ form.errors.attachments }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Ticket' }}
                            </Button>
                            <Button type="button" variant="outline" @click="router.visit(route('tickets.index'))">
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
