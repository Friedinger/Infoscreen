<template>
    <departure :config="config" />
    <info-text />
    <news :config="config" />
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import departure from "./components/departure.vue";
import type { Config } from "./types/config";
import news from "./components/news.vue";
import infoText from "./components/infoText.vue";

const defaultConfig: Config = {
    reloadInterval: 60,
    newsInterval: 20,
    departureInterval: 20,
    news: [],
    departureUrl: "",
};
const config = ref<Config>(defaultConfig);

onMounted(() => {
    loadConfig();
    setInterval(loadConfig, config.value.reloadInterval * 1000);
});

function loadConfig() {
    fetch("config.json?t=" + new Date().getTime())
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP status: ${response.status}`);
            return response.json();
        })
        .then((data) => {
            config.value = data;
        })
        .catch((error) => {
            console.error("Error loading configuration file: ", error);
            config.value = defaultConfig;
        });
}
</script>

<style>
body {
    aspect-ratio: 16 / 9;
    width: 100%;
    height: 100vh;
    margin: 0;
    overflow: hidden;
    background: hsl(306, 57%, 30%);
    font-family: sans-serif;
}
body > #app {
    display: flex;
    height: 100%;
}
img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    vertical-align: middle;
}
</style>
