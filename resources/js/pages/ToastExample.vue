<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { ToastProvider } from '@/components/ui/toast'
import { useToast } from '@/composables/useToast'

const { success, error, warning, info, addToast } = useToast()

const showSuccessToast = () => {
  success('Success!', 'Your action was completed successfully.')
}

const showErrorToast = () => {
  error('Error!', 'Something went wrong. Please try again.')
}

const showWarningToast = () => {
  warning('Warning!', 'Please review your input before proceeding.')
}

const showInfoToast = () => {
  info('Information', 'Here is some useful information for you.')
}

const showCustomToast = () => {
  addToast({
    title: 'Custom Toast',
    description: 'This is a custom toast with an action button.',
    variant: 'default',
    duration: 10000,
    action: {
      label: 'Undo',
      onClick: () => {
        console.log('Undo action clicked!')
      }
    }
  })
}
</script>

<template>
  <Head title="Toast Example" />

  <AppLayout>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Toast Examples</h1>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <Button @click="showSuccessToast" variant="default">
          Show Success Toast
        </Button>
        
        <Button @click="showErrorToast" variant="destructive">
          Show Error Toast
        </Button>
        
        <Button @click="showWarningToast" variant="outline">
          Show Warning Toast
        </Button>
        
        <Button @click="showInfoToast" variant="secondary">
          Show Info Toast
        </Button>
        
        <Button @click="showCustomToast" variant="ghost">
          Show Custom Toast
        </Button>
      </div>
    </div>

    <!-- Toast Provider - must be included in your app -->
    <ToastProvider />
  </AppLayout>
</template> 