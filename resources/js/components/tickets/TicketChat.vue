<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { Send,  User, Clock } from 'lucide-vue-next';
import { ref, nextTick, onMounted, computed } from 'vue';
import { Ticket, TicketComment } from '@/types/ticket';
import type { authUser } from '@/types/index';
import { useToast } from '@/composables/useToast';
import { useTicketStatus } from '@/composables/useTicketStatus';
import { useTicketFormatting } from '@/composables/useTicketFormatting';
import { useTicketPermissions } from '@/composables/useTicketPermissions';
import axios from 'axios';


interface Props {
    ticket: Ticket;
    authUser: authUser;
}

const props = defineProps<Props>();
const newMessage = ref('');
const localComments = ref<TicketComment[]>([]);
const messagesContainer = ref<HTMLElement>();
const currentPage = ref(1);
const hasMoreComments = ref(true);
const isLoadingComments = ref(false);
const { error: showError } = useToast();

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const handleScroll = () => {
    if (!messagesContainer.value || isLoadingComments.value || !hasMoreComments.value) return;
    
    if (messagesContainer.value.scrollTop < 100) {
        loadMoreComments();
    }
};

const loadComments = async () => {
    isLoadingComments.value = true;
    
    try {
        const { data } = await axios.get(route('tickets.comments.show', { ticket: props.ticket.id }));
        
        if (data.data && data.data.length > 0) {
            localComments.value = data.data.reverse();
            currentPage.value = data.current_page;
            hasMoreComments.value = data.current_page < data.last_page;
        } else {
            hasMoreComments.value = false;
        }
        
        scrollToBottom();
    } catch (error: any) {
        console.error('Error fetching comments:', error);
        showError('Error', error.response?.data?.message || 'Error fetching comments');
    } finally {
        isLoadingComments.value = false;
    }
};

const loadMoreComments = async () => {
    if (isLoadingComments.value || !hasMoreComments.value) return;
    
    const previousScrollHeight = messagesContainer.value?.scrollHeight || 0;
    
    isLoadingComments.value = true;
    
    try {
        const { data } = await axios.get(route('tickets.comments.show', { ticket: props.ticket.id }), {
            params: { page: currentPage.value + 1 }
        });
        
        if (data.data && data.data.length > 0) {
            localComments.value.unshift(...data.data.reverse());
            currentPage.value = data.current_page;
            hasMoreComments.value = data.current_page < data.last_page;
            
            nextTick(() => {
                if (messagesContainer.value) {
                    const newScrollHeight = messagesContainer.value.scrollHeight;
                    messagesContainer.value.scrollTop = newScrollHeight - previousScrollHeight;
                }
            });
        } else {
            hasMoreComments.value = false;
        }
    } catch (error: any) {
        console.error('Error fetching comments:', error);
        showError('Error', error.response?.data?.message || 'Error fetching comments');
    } finally {
        isLoadingComments.value = false;
    }
};

const { isTicketClosedOrCanceled } = useTicketStatus(props.ticket);

const { formatStatus, formatPriority, getStatusVariant, getPriorityVariant } = useTicketFormatting();
const { canComment } = useTicketPermissions(props.authUser);

const sendMessage = async () => {
    if (newMessage.value.trim()) {
        try {
            const { data } = await axios.post(route('tickets.comments.store', { ticket: props.ticket.id }), {
                comment: newMessage.value.trim()
            });
            
            if (data.status === 'success' && data.comment) {
                localComments.value.push(data.comment);
                newMessage.value = '';
                scrollToBottom();
            }
        } catch (error) {
            showError('Error', (error as any).response.data.error + ' ' + (error as any).response.data.message);
        }
    }
};

const handleKeyPress = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
};

onMounted(() => {
    loadComments();
});
</script>

<template>
    <Card class="h-full flex flex-col">
        <CardHeader class="border-b">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div>
                    <CardTitle class="text-base md:text-lg">{{ ticket.code }}</CardTitle>
                    <p class="text-xs md:text-sm text-muted-foreground mt-1">{{ ticket.title }}</p>
                </div>
                <div class="flex items-center gap-1 md:gap-2">
                    <Badge 
                        :variant="getStatusVariant(ticket.status) as any"
                        class="text-xs"
                    >
                        {{ formatStatus(ticket.status) }}
                    </Badge>
                    <Badge 
                        :variant="getPriorityVariant(ticket.priority) as any"
                        class="text-xs"
                    >
                        {{ formatPriority(ticket.priority) }}
                    </Badge>
                </div>
            </div>
        </CardHeader>
        
        <CardContent class="flex-1 flex flex-col p-0 min-h-0">
            <!-- Messages Area -->
            <div 
                ref="messagesContainer"
                class="flex-1 p-2 md:p-4 space-y-4 overflow-y-auto min-h-0" 
                style="max-height: 60vh;"
                @scroll="handleScroll"
            >
                <!-- Loading Indicator -->
                <div v-if="isLoadingComments" class="flex justify-center py-2">
                    <span class="text-xs text-muted-foreground">Loading...</span>
                </div>

                <div
                    v-for="comment in localComments"
                    :key="comment.id"
                    class="flex gap-3"
                    :class="{ 'flex-row-reverse': comment.user_id === authUser.id }"
                >
                    <div class="w-8 h-8 bg-muted rounded-full flex items-center justify-center flex-shrink-0">
                        <User class="w-4 h-4 text-muted-foreground" />
                    </div>
                    <div class="flex-1" :class="{ 'text-right': comment.user_id === authUser.id }">
                        <div class="flex items-center gap-2 mb-1" :class="{ 'justify-end': comment.user_id === authUser.id }">
                            <span class="text-sm font-medium">{{ comment.user?.name || `User ${comment.user_id}` }}</span>
                            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Clock class="w-3 h-3" />
                                {{ new Date(comment.created_at).toLocaleString('en-US') }}
                            </div>
                        </div>
                        <div 
                            class="rounded-lg p-3 inline-block"
                            :class="comment.user_id === authUser.id 
                                ? 'bg-primary text-primary-foreground ml-auto' 
                                : 'bg-muted'"
                        >
                            <p class="text-sm">{{ comment.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t p-2 md:p-4">
                <div class="flex gap-2">
                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger asChild>
                                <textarea
                                    v-model="newMessage"
                                    placeholder="Type your message..."
                                    class="flex-1 min-h-[80px] max-h-32 resize-none rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    rows="1"
                                    :disabled="isTicketClosedOrCanceled || !canComment"
                                    @keydown="handleKeyPress"
                                />
                            </TooltipTrigger>
                            <TooltipContent v-if="!canComment">
                                <p>Observers cannot send comments</p>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                    <Button @click="sendMessage" :disabled="!newMessage.trim() || isTicketClosedOrCanceled">
                        <Send class="w-4 h-4" />
                    </Button>
                </div>
            </div>
        </CardContent>
    </Card>
</template> 