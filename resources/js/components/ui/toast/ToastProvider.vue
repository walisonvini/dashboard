<script setup lang="ts">
import { useToast } from '@/composables/useToast'
import { Toast, ToastAction, ToastClose, ToastDescription, ToastTitle, ToastViewport } from '.'

const { toasts, removeToast } = useToast()
</script>

<template>
  <ToastViewport>
    <Toast
      v-for="toast in toasts"
      :key="toast.id"
      :variant="toast.variant"
      class="group"
    >
      <div class="grid gap-1">
        <ToastTitle v-if="toast.title">
          {{ toast.title }}
        </ToastTitle>
        <ToastDescription v-if="toast.description">
          {{ toast.description }}
        </ToastDescription>
      </div>
      
      <ToastAction
        v-if="toast.action"
        :alt-text="toast.action.label"
        @click="toast.action.onClick"
      >
        {{ toast.action.label }}
      </ToastAction>
      
      <ToastClose @click="removeToast(toast.id)" />
    </Toast>
  </ToastViewport>
</template> 