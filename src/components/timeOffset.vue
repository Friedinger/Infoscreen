<template>
    {{ formattedTime }}
</template>

<script setup lang="ts">
import { computed, defineProps } from "vue";

const props = defineProps<{ time: number }>();

const formattedTime = computed(() => {
    const offset = props.time - new Date().getTime();
    const minutes = Math.round(offset / 1000 / 60);
    if (minutes == 0) return "Jetzt";
    return minutes + " min";
});
</script>
