<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-blue-600 dark:text-blue-500">
            {{ message }}
        </p>
    </div>
</template>
