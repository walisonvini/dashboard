<script setup lang="ts">
interface Props {
    size?: 'sm' | 'md' | 'lg';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    class: '',
});

const sizeClasses = {
    sm: 'h-4 w-4',
    md: 'h-6 w-6',
    lg: 'h-8 w-8',
};
</script>

<template>
    <div
        :class="[
            'animate-spin rounded-full border-2 border-current border-t-transparent',
            sizeClasses[props.size],
            props.class
        ]"
        role="status"
        aria-label="Loading"
    >
        <span class="sr-only">Loading...</span>
    </div>
</template>
