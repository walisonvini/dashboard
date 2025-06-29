import { usePage } from '@inertiajs/vue3'
import { useToast } from './useToast'
import { watch } from 'vue'

interface FlashMessages {
  success?: string
  error?: string
  warning?: string
  info?: string
}

export function useFlashMessages() {
  const page = usePage()
  const { success, error, warning, info } = useToast()

  // Watch for flash messages and show toasts
  watch(
    () => page.props.flash as FlashMessages,
    (flash) => {
      if (flash?.success) {
        success('Success!', flash.success)
      }
      if (flash?.error) {
        error('Error!', flash.error)
      }
      if (flash?.warning) {
        warning('Warning!', flash.warning)
      }
      if (flash?.info) {
        info('Information', flash.info)
      }
    },
    { immediate: true, deep: true }
  )

  return {
    flash: page.props.flash as FlashMessages
  }
} 