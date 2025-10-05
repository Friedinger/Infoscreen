<template>
    <div class="line">
        <span :class="`type ${departure.transportType}`"></span>
        <span class="label">{{ departure.label }}</span>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from "vue";
const props = defineProps<{
    departure: {
        transportType: string;
        label: string;
    };
}>();
</script>

<style scoped>
.line {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: bold;
}
.type {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    background-position: center;
    background-size: contain;
    background-repeat: no-repeat;
}
.UBAHN {
    background-image: url(https://www.mvg.de/.resources/mvgde-icons/color/transport-ubahn.svg);
}
.SBAHN {
    background-image: url(https://www.mvg.de/.resources/mvgde-icons/color/transport-sbahn.svg);
}
.BUS {
    background-image: url(https://www.mvg.de/.resources/mvgde-icons/color/transport-bus.svg);
}
.TRAM {
    background-image: url(https://www.mvg.de/.resources/mvgde-icons/color/transport-tram.svg);
}
</style>
