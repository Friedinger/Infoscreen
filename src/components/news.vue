<template>
    <div>
        <img alt="News" :src="currentSrc ? `news/${currentSrc}` : ''" />
    </div>
</template>

<script setup lang="ts">
import { onUnmounted, ref, watch } from "vue";
import type { Config } from "../types/config";

const props = defineProps<{
    config: Config;
}>();

const currentSrc = ref("");
const currentIndex = ref(0);
const interval = ref<ReturnType<typeof setInterval>>();

function news() {
    if (interval.value) {
        clearInterval(interval.value);
        interval.value = undefined;
    }
    const { news, newsInterval } = props.config;
    if (!news || news.length === 0) {
        currentSrc.value = "";
        return;
    }
    currentIndex.value = 0;
    currentSrc.value = news[currentIndex.value];
    if (news.length > 1 && newsInterval > 0) {
        interval.value = setInterval(() => {
            currentIndex.value = (currentIndex.value + 1) % news.length;
            currentSrc.value = news[currentIndex.value];
        }, newsInterval * 1000);
    }
}

watch(() => [props.config.news, props.config.newsInterval], news, {
    immediate: true,
});

onUnmounted(() => {
    if (interval.value) clearInterval(interval.value);
});
</script>

<style scoped>
div {
    display: flex;
    position: relative;
    align-items: center;
    justify-content: center;
    aspect-ratio: 1 / 1.414;
    max-width: 100%;
    height: 100%;
    overflow: hidden;
}
img {
    width: auto;
    height: 100%;
}
img[src=""] {
    scale: 0.75;
    content: url(https://cdn.prod.website-files.com/64d11728a06601a00aeea217/64d11cdeec6fe190c94e7650_lgo%20azubiwerk%20header.svg);
    filter: brightness(0);
}
</style>
