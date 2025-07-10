<script setup lang="ts">
interface Props {
  variant?: 'default' | 'secondary' | 'destructive' | 'outline' | 'low' | 'medium' | 'high' | 'open' | 'in_progress' | 'closed' | 'dash';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default'
});

const variantClasses = {
  default: 'border-gray-800 text-gray-800 dark:border-neutral-200 dark:text-white',
  secondary: 'border-purple-700 text-purple-700 dark:border-purple-200 dark:text-purple-200',
  destructive: 'border-rose-700 text-rose-700 dark:border-rose-200 dark:text-rose-200',
  outline: 'border-gray-400 text-gray-600 dark:border-gray-200 dark:text-gray-200',
  low: 'border-green-700 text-green-700 dark:border-green-200 dark:text-green-200',
  medium: 'border-yellow-700 text-yellow-700 dark:border-yellow-200 dark:text-yellow-200',
  high: 'border-red-700 text-red-700 dark:border-red-200 dark:text-red-200',
  open: 'border-blue-700 text-blue-700 dark:border-blue-200 dark:text-blue-200',
  in_progress: 'border-orange-700 text-orange-700 dark:border-orange-200 dark:text-orange-200',
  closed: 'border-gray-500 text-gray-500 dark:border-gray-300 dark:text-gray-300',
  dash: 'border-dashed border-2 border-gray-800 text-gray-800 dark:border-neutral-200 dark:text-white'
};

const baseClasses = 'inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium border';
</script>

<template>
  <span
    :class="[
      baseClasses,
      variantClasses[variant]
    ]"
  >
    <slot />
  </span>
</template> 