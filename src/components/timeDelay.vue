<template>
    <span v-if="formattedTime > 0" class="delayed">
        + {{ formattedTime }} min
    </span>
    <span v-else-if="formattedTime < 0">
        - {{ Math.abs(formattedTime) }} min
    </span>
    <span v-else> pünktlich</span>
</template>

<script setup lang="ts">
import { computed, defineProps } from "vue";

const props = defineProps<{ timeReal: number; timePlanned: number }>();

const formattedTime = computed(() => {
    const time = props.timeReal - props.timePlanned;
    return Math.round(time / 1000 / 60);
});
</script>

<style scoped>
span {
    color: green;
}
.delayed {
    color: red;
}
</style>
