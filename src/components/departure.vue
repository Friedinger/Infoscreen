<template>
    <v-container class="pa-2">
        <v-row
            class="font-weight-bold text-uppercase"
            style="background-color: hsl(306, 57%, 30%); color: white">
            <v-col cols="2">Linie</v-col>
            <v-col cols="4">Ziel</v-col>
            <v-col cols="2">Halt</v-col>
            <v-col cols="4">Zeit</v-col>
        </v-row>
        <v-row
            v-for="(departure, index) in departures"
            :key="index"
            class="align-center"
            :style="{
                backgroundColor:
                    index % 2 === 0 ? 'hsl(0, 0%, 90%)' : 'hsl(0, 0%, 80%)',
            }">
            <v-col cols="2">
                <transport-line :departure="departure" />
            </v-col>
            <v-col cols="4">
                {{ departure.destination }}
            </v-col>
            <v-col cols="2">
                {{ departure.platform }}
            </v-col>
            <v-col cols="4">
                <v-row no-gutters>
                    <v-col cols="4">
                        <time-clock :time="departure.realtimeDepartureTime" />
                    </v-col>
                    <v-col cols="4">
                        <time-offset :time="departure.realtimeDepartureTime" />
                    </v-col>
                </v-row>
                <v-row no-gutters>
                    <v-col>
                        <time-delay
                            :timeReal="departure.realtimeDepartureTime"
                            :timePlanned="departure.plannedDepartureTime" />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import { Departure } from "../types/departure";
import timeClock from "./timeClock.vue";
import timeOffset from "./timeOffset.vue";
import timeDelay from "./timeDelay.vue";
import { ref, onMounted, watch } from "vue";
import transportLine from "./transportLine.vue";
import { Config } from "../types/config";

const props = defineProps<{
    config: Config;
}>();
const departures = ref<Departure[]>();
const interval = ref<ReturnType<typeof setInterval>>();

onMounted(() => {
    init();
});

watch(
    () => props.config.departureUrl,
    () => init()
);

function init() {
    fetchDepartures();
    if (interval.value) clearInterval(interval.value);
    interval.value = setInterval(fetchDepartures, 10 * 1000);
}

function fetchDepartures() {
    fetch(props.config.departureUrl + "&t=" + new Date().getTime())
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP status: ${response.status}`);
            return response.json();
        })
        .then((content: Departure[]) => {
            departures.value = content.filter(
                (departure) =>
                    departure.realtimeDepartureTime >= new Date().getTime()
            );
        })
        .catch((error) => {
            console.error("Error updating departure data: ", error);
        });
}
</script>

<style scoped>
.v-container {
    flex: 0 0 50%;
    width: 100%;
    height: 100%;
    overflow-y: hidden;
    border-collapse: collapse;
    font-size: 1.5rem;
}
</style>
