<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Paperclip } from 'lucide-vue-next';
import { TicketAttachment } from '@/types/ticket';
import { ref } from 'vue';
import axios from 'axios';
import { useToast } from '@/composables/useToast';

interface Props {
    ticket: { id: number };
    attachments: TicketAttachment[];
}

const props = defineProps<Props>();
const fileInput = ref<HTMLInputElement>();
const { error: showError } = useToast();
const localAttachments = ref<TicketAttachment[]>([...props.attachments]);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const selectedFiles = ref<File[]>([]);

const handleFileUpload = async () => {
    if (selectedFiles.value.length === 0) return;
    
    const formData = new FormData();
    selectedFiles.value.forEach(file => {
        formData.append('files[]', file);
    });
    
    try {
        const { data } = await axios.post(route('tickets.attachments.store', { ticket: props.ticket.id }), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        if (data.status === 'success' && data.attachments) {
            data.attachments.forEach((attachment: TicketAttachment) => {
                localAttachments.value.push(attachment);
            });
            selectedFiles.value = [];
        }
    } catch (error: any) {
        if (error.response?.data?.message) {
            showError('Upload Error', error.response.data.message);
        } else if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            const errorMessages = Object.values(errors).flat();
            showError('Validation Error', errorMessages.join(', '));
        } else {
            showError('Upload Error', 'Could not upload files. Please try again.');
        }
    }
};

const handleFileSelection = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        if (files.length > 5) {
            showError('Error', 'Maximum 5 files allowed.');
            return;
        }
        selectedFiles.value = files;
    }
};

const downloadAttachment = (attachment: TicketAttachment) => {
  const url = route('tickets.attachments.download', { attachment: attachment.id });
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', '');
  link.setAttribute('target', '_self');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

</script>

<template>
    <Card class="h-full flex flex-col">
        <input
            ref="fileInput"
            type="file"
            multiple
            class="hidden"
            @change="handleFileSelection"
            accept="*/*"
        />
        <CardHeader>
            <div class="flex items-center justify-between">
                <CardTitle class="text-lg">Attachments</CardTitle>
                <Button size="sm" variant="outline" @click="triggerFileInput">
                    <Paperclip class="w-4 h-4 mr-2" />
                    Add Files
                </Button>
            </div>
        </CardHeader>
        <CardContent class="flex-1 flex flex-col p-0 min-h-0 space-y-3">
            <!-- Selected Files Preview -->
            <div v-if="selectedFiles.length > 0" class="border-b p-3 bg-muted/30">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Selected Files ({{ selectedFiles.length }}/5)</span>
                    <Button size="sm" @click="handleFileUpload" :disabled="selectedFiles.length === 0">
                        Upload Files
                    </Button>
                </div>
                <div class="flex flex-wrap gap-1">
                    <span 
                        v-for="file in selectedFiles" 
                        :key="file.name" 
                        class="inline-block bg-primary/10 text-primary rounded px-2 py-1 text-xs"
                    >
                        {{ file.name }}
                    </span>
                </div>
            </div>

            <!-- Attachments List -->
            <div class="flex-1 p-3 space-y-3 overflow-y-auto min-h-0" style="max-height: 75vh;">
                <div v-if="localAttachments.length === 0" class="text-center py-8 text-muted-foreground">
                    <Paperclip class="w-8 h-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">No attachments yet</p>
                </div>
                <div v-else class="space-y-3">
                    <div
                        v-for="attachment in localAttachments"
                        :key="attachment.id"
                        class="flex items-center gap-3 p-3 border rounded-lg hover:bg-accent/50 transition-colors cursor-pointer"
                        @click="downloadAttachment(attachment)"
                    >
                        <Paperclip class="w-4 h-4 text-muted-foreground flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ attachment.original_name }}</p>
                            <p class="text-xs text-muted-foreground">{{ new Date(attachment.created_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 