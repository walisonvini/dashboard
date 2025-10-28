<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Log } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Copy, ChevronsDown, ChevronsUp } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Logs',
        href: '/logs',
    },
];

const props = defineProps<{
    log: Log;
}>();

const isJsonCollapsed = ref(false);

const prettyJson = computed(() => {
    try {
        return JSON.stringify(props.log.data ?? {}, null, 2);
    } catch (e) {
        return String(props.log.data ?? '');
    }
});

async function copyJson() {
    try {
        await navigator.clipboard.writeText(prettyJson.value);
    } catch (e) {
        const ta = document.createElement('textarea');
        ta.value = prettyJson.value;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }
}

</script>

<template>
    <Head title="Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('logs.index')">
                        <Button variant="ghost" size="icon">
                            <ArrowLeft class="h-4 w-4" />
                        </Button>
                    </Link>
                    <h1 class="text-2xl font-bold">Log #{{ props.log.id }}</h1>
                </div>
                <div class="text-sm text-muted-foreground">{{ new Date(props.log.created_at).toLocaleString() }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-md border p-4">
                    <h3 class="font-semibold mb-4">Details</h3>

                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <div class="space-y-2">
                                <div class="text-sm"><strong>Actor:</strong> <span class="text-muted-foreground">{{ props.log.user?.name ?? '—' }}</span></div>
                                <div class="text-sm"><strong>Email:</strong> <span class="text-muted-foreground">{{ props.log.user?.email ?? '—' }}</span></div>
                                <div class="text-sm"><strong>Action:</strong> <span class="text-muted-foreground">{{ props.log.action ?? '—' }}</span></div>
                                <div class="text-sm"><strong>Model:</strong> <span class="text-muted-foreground">{{ props.log.model ?? '—' }}</span></div>
                                <div class="text-sm"><strong>Model ID:</strong> <span class="text-muted-foreground">{{ props.log.model_id ?? '—' }}</span></div>
                                <div class="text-sm"><strong>IP:</strong> <span class="text-muted-foreground">{{ props.log.ip_address ?? '—' }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 rounded-md border p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold">Data</h3>
                        <div class="flex items-center gap-2">
                            <Button size="sm" variant="outline" @click="copyJson">
                                <Copy class="h-4 w-4 mr-2" /> Copy JSON
                            </Button>
                            <Button size="sm" variant="outline" @click="isJsonCollapsed = !isJsonCollapsed">
                                <span v-if="isJsonCollapsed"><ChevronsDown class="h-4 w-4" /></span>
                                <span v-else><ChevronsUp class="h-4 w-4" /></span>
                            </Button>
                        </div>
                    </div>

                    <div>
                        <pre
                            class="bg-muted p-3 rounded text-sm overflow-auto font-mono"
                            :style="{ 'maxHeight': isJsonCollapsed ? '10rem' : 'none' }">
<code>{{ prettyJson }}</code>
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
