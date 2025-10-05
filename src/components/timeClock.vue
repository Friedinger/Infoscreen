<template>
    {{ formattedTime }}
</template>

<script setup lang="ts">
import { computed, defineProps } from "vue";

const props = defineProps<{ time: number }>();

const formattedTime = computed(() => {
    return new Date(props.time).toLocaleTimeString("de-DE", {
        hour: "numeric",
        minute: "numeric",
    });
});
</script>
