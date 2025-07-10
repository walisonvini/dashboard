<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { ref } from 'vue';

interface Attachment {
  name: string;
  url: string;
}
interface Comment {
  id: number;
  user: { id: number; name: string };
  created_at: string;
  body: string;
  attachments?: Attachment[];
}
interface Ticket {
  id: number;
  title: string;
  description: string;
  status: string;
  priority: string;
  category: { id: number; name: string };
  created_at: string;
  initial_comment?: string;
  attachments?: Attachment[];
  comments?: Comment[];
}

const props = defineProps<{ ticket: Ticket }>();

const form = useForm({
  comment: '',
  attachments: [] as File[],
});

function handleCommentFiles(e: Event) {
  const target = e.target as HTMLInputElement;
  if (target.files) {
    form.attachments = Array.from(target.files);
  }
}
function submitComment() {
  form.post(route('comments.store', props.ticket.id), {
    forceFormData: true,
    onSuccess: () => {
      form.reset();
    }
  });
}
</script>

<template>
  <Head :title="`Ticket #${props.ticket.id}`" />
  <AppLayout>
    <div class="max-w-5xl mx-auto mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Detalhes do ticket -->
      <section class="md:col-span-1 bg-white dark:bg-neutral-900 rounded-xl shadow border border-gray-100 dark:border-neutral-800 p-6 flex flex-col gap-4">
        <div>
          <h1 class="text-2xl font-bold mb-2">{{ props.ticket.title }}</h1>
          <div class="flex gap-2 mb-2">
            <Badge :variant="props.ticket.status === 'open' ? 'open' : props.ticket.status === 'in_progress' ? 'in_progress' : 'closed'">
              {{ props.ticket.status }}
            </Badge>
            <Badge :variant="props.ticket.priority">
              {{ props.ticket.priority }}
            </Badge>
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">Criado em: {{ new Date(props.ticket.created_at).toLocaleString() }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 dark:text-gray-400">Categoria</div>
          <div class="font-medium">{{ props.ticket.category.name }}</div>
        </div>
        <div v-if="props.ticket.initial_comment">
          <div class="text-xs text-gray-500 dark:text-gray-400">Comentário inicial</div>
          <div class="font-medium">{{ props.ticket.initial_comment }}</div>
        </div>
        <div v-if="props.ticket.description">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Descrição</div>
          <div class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ props.ticket.description }}</div>
        </div>
        <div v-if="props.ticket.attachments && props.ticket.attachments.length">
          <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Anexos do ticket</div>
          <ul class="flex flex-wrap gap-2">
            <li v-for="file in props.ticket.attachments" :key="file.name">
              <a :href="file.url" target="_blank" class="inline-block bg-primary-100 text-white rounded px-2 py-1 text-xs font-medium hover:underline">
                {{ file.name }}
              </a>
            </li>
          </ul>
        </div>
      </section>

      <!-- Comentários -->
      <section class="md:col-span-2 flex flex-col gap-6">
        <div class="bg-white dark:bg-neutral-900 rounded-xl shadow border border-gray-100 dark:border-neutral-800 p-6">
          <div class="text-lg font-semibold mb-4">Comentários</div>
          <div class="space-y-6 max-h-[500px] overflow-y-auto">
            <div v-if="!props.ticket.comments || !props.ticket.comments.length" class="text-gray-400 text-sm">Nenhum comentário ainda.</div>
            <div v-for="c in props.ticket.comments" :key="c.id" class="flex flex-col gap-1 border-b border-gray-100 dark:border-neutral-800 pb-4 last:border-0">
              <div class="flex items-center gap-2">
                <span class="font-semibold text-sm">{{ c.user.name }}</span>
                <span class="text-xs text-gray-400">{{ new Date(c.created_at).toLocaleString() }}</span>
              </div>
              <div class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ c.body }}</div>
              <div v-if="c.attachments && c.attachments.length" class="flex flex-wrap gap-2 mt-1">
                <a v-for="file in c.attachments" :key="file.name" :href="file.url" target="_blank" class="inline-block bg-primary-100 text-white rounded px-2 py-1 text-xs font-medium hover:underline">
                  {{ file.name }}
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Novo comentário -->
        <form @submit.prevent="submitComment" class="bg-white dark:bg-neutral-900 rounded-xl shadow border border-gray-100 dark:border-neutral-800 p-6 space-y-2">
          <div class="text-xs text-gray-500 dark:text-gray-400">Adicionar comentário</div>
          <textarea v-model="form.comment" rows="3" class="w-full rounded border border-gray-300 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800 p-2 text-sm" placeholder="Digite seu comentário..."></textarea>
          <input type="file" multiple @change="handleCommentFiles" class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground file:shadow-xs file:hover:bg-primary/90"/>
          <div v-if="form.attachments.length" class="flex flex-wrap gap-2">
            <span v-for="file in form.attachments" :key="file.name" class="inline-block bg-primary-100 rounded px-2 py-1 text-xs text-white">
              {{ file.name }}
            </span>
          </div>
          <div>
            <button type="submit" class="mt-2 bg-primary text-primary-foreground px-4 py-1.5 rounded font-medium text-sm shadow hover:bg-primary/90 transition">Comentar</button>
          </div>
        </form>
        <div>
          <Link :href="route('tickets.index')" class="text-sm text-primary underline hover:text-primary/80">← Voltar para lista</Link>
        </div>
      </section>
    </div>
  </AppLayout>
</template>