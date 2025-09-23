<script setup lang="ts">
import { PaginationEllipsis, PaginationFirst, PaginationLast, PaginationList, PaginationListItem, PaginationNext, PaginationPrev, PaginationRoot } from 'reka-ui'
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'
import { computed } from 'vue'

interface Props {
  currentPage: number
  totalItems: number
  itemsPerPage: number
  showEdges?: boolean
  siblingCount?: number
}

const props = withDefaults(defineProps<Props>(), {
  showEdges: true,
  siblingCount: 2,
})

const responsiveSiblingCount = computed(() => {
  return props.siblingCount
})

const emit = defineEmits<{
  'update:currentPage': [page: number]
}>()

const handlePageChange = (page: number) => {
  emit('update:currentPage', page)
}
</script>

<template>
  <PaginationRoot
    :page="currentPage"
    :total="totalItems"
    :items-per-page="itemsPerPage"
    :show-edges="showEdges"
    :sibling-count="responsiveSiblingCount"
    @update:page="handlePageChange"
  >
    <PaginationList
      v-slot="{ items }"
      class="flex items-center gap-1 text-stone-700 dark:text-white"
    >
      <PaginationFirst v-if="showEdges" class="w-9 h-9 flex items-center justify-center bg-background border border-input rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors disabled:opacity-50 hidden sm:flex">
        <ChevronsLeft class="h-4 w-4" />
      </PaginationFirst>
      
      <PaginationPrev class="w-9 h-9 flex items-center justify-center bg-background border border-input rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors mr-2 sm:mr-4 disabled:opacity-50">
        <ChevronLeft class="h-4 w-4" />
      </PaginationPrev>
      
      <template v-for="(page, index) in items" :key="index">
        <PaginationListItem
          v-if="page.type === 'page'"
          class="w-9 h-9 border border-input rounded-lg bg-background hover:bg-accent hover:text-accent-foreground data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[selected]:border-primary transition-colors"
          :value="page.value"
        >
          {{ page.value }}
        </PaginationListItem>
        
        <PaginationEllipsis
          v-else
          :index="index"
          class="w-9 h-9 flex items-center justify-center"
        >
          &#8230;
        </PaginationEllipsis>
      </template>
      
      <PaginationNext class="w-9 h-9 flex items-center justify-center bg-background border border-input rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors ml-2 sm:ml-4 disabled:opacity-50">
        <ChevronRight class="h-4 w-4" />
      </PaginationNext>
      
      <PaginationLast v-if="showEdges" class="w-9 h-9 flex items-center justify-center bg-background border border-input rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors disabled:opacity-50 hidden sm:flex">
        <ChevronsRight class="h-4 w-4" />
      </PaginationLast>
    </PaginationList>
  </PaginationRoot>
</template>