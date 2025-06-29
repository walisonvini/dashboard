import { ref } from 'vue'

export interface Toast {
  id: string
  title?: string
  description?: string
  variant?: 'default' | 'destructive' | 'success' | 'warning' | 'info'
  duration?: number
  action?: {
    label: string
    onClick: () => void
  }
}

const toasts = ref<Toast[]>([])

export function useToast() {
  const addToast = (toast: Omit<Toast, 'id'>) => {
    const id = Math.random().toString(36).substr(2, 9)
    const newToast: Toast = {
      id,
      duration: 5000,
      variant: 'default',
      ...toast,
    }
    
    toasts.value.push(newToast)
    
    // Auto remove after duration
    if (newToast.duration && newToast.duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, newToast.duration)
    }
    
    return id
  }
  
  const removeToast = (id: string) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }
  
  const clearToasts = () => {
    toasts.value = []
  }
  
  // Convenience methods
  const success = (title: string, description?: string) => {
    return addToast({ title, description, variant: 'success' })
  }
  
  const error = (title: string, description?: string) => {
    return addToast({ title, description, variant: 'destructive' })
  }
  
  const warning = (title: string, description?: string) => {
    return addToast({ title, description, variant: 'warning' })
  }
  
  const info = (title: string, description?: string) => {
    return addToast({ title, description, variant: 'info' })
  }
  
  return {
    toasts,
    addToast,
    removeToast,
    clearToasts,
    success,
    error,
    warning,
    info,
  }
} 