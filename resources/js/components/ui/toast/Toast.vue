<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { Primitive, type PrimitiveProps } from 'reka-ui'
import { type ToastVariants, toastVariants } from '.'

interface Props extends PrimitiveProps {
  variant?: ToastVariants['variant']
  class?: HTMLAttributes['class']
}

const props = withDefaults(defineProps<Props>(), {
  as: 'div',
})
</script>

<template>
  <Primitive
    data-slot="toast"
    :as="as"
    :as-child="asChild"
    :class="cn(toastVariants({ variant }), props.class)"
  >
    <slot />
  </Primitive>
</template> 