<template>
    <v-table striped="even" fixed-header>
        <thead>
            <tr>
                <th scope="col">Linie</th>
                <th scope="col">Ziel</th>
                <th scope="col">Halt</th>
                <th scope="col">Real</th>
                <th scope="col">In</th>
                <th scope="col">Verspätung</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </v-table>
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
.v-table {
    flex: 0 0 50%;
    width: 100%;
    height: 100%;
    overflow-y: hidden;
    border-collapse: collapse;
    font-size: 1.5rem;
}
::v-deep table th {
    background-color: hsl(306, 57%, 30%) !important;
    color: white;
    font-weight: 800 !important;
    text-align: left;
    text-transform: uppercase;
}
</style>
