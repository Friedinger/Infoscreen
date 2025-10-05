<template>
    <table>
        <tr class="th">
            <th>Linie</th>
            <th>Ziel</th>
            <th>Halt</th>
            <th>Real</th>
            <th>In</th>
            <th>Verspätung</th>
        </tr>
        <tr v-for="(departure, index) in departures" :key="index">
            <td>
                <transport-line :departure="departure" />
            </td>
            <td>{{ departure.destination }}</td>
            <td>
                {{ departure.platform }}
            </td>
            <td>
                <time-clock :time="departure.realtimeDepartureTime" />
            </td>
            <td>
                <time-offset :time="departure.realtimeDepartureTime" />
            </td>
            <td>
                <time-delay
                    :timeReal="departure.realtimeDepartureTime"
                    :timePlanned="departure.plannedDepartureTime" />
            </td>
        </tr>
    </table>
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
table {
    flex: 0 0 50%;
    width: 100%;
    height: 100%;
    overflow-y: hidden;
    border-collapse: collapse;
}
table th,
table td {
    height: 3rem;
    padding: 0.5rem;
    font-size: 1.3rem;
}
table td:first-child,
table th:first-child {
    padding-left: 1rem;
}
table td:last-child,
table th:last-child {
    padding-right: 1rem;
}
table th {
    background-color: hsl(306, 57%, 30%);
    color: white;
    font-weight: 800;
    text-align: left;
    text-transform: uppercase;
}
table tr:nth-child(even) {
    background-color: hsl(0, 0%, 90%);
}
table tr:nth-child(odd) {
    background-color: hsl(0, 0%, 80%);
}
</style>
