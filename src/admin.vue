<template>
    <h1>Infoscreen Admin</h1>
    <p><a href="../">Zurück zum Infoscreen</a></p>

    <div v-if="message" class="message">{{ message }}</div>

    <h3>News hinzufügen</h3>
    <p>
        Es können nur Bilder hochgeladen werden mit einer Größe von maximal
        10MB.
    </p>
    <form @submit.prevent="addNews">
        <input type="file" @change="handleFileChange" required />
        <input type="submit" value="Hochladen" />
    </form>
    <br />

    <h3>News löschen</h3>
    <form @submit.prevent="deleteNews">
        <select v-model="newsToDelete" required>
            <option value="">-- Bitte wählen --</option>
            <option v-for="news in config?.news" :key="news" :value="news">
                {{ news }}
            </option>
        </select>
        <input type="submit" value="Löschen" />
    </form>
    <br />

    <h3>Einstellungen</h3>
    <form @submit.prevent="updateSettings" v-if="config">
        <label for="departureUrl">Abfahrten API URL</label>
        <input
            type="url"
            id="departureUrl"
            v-model="config.departureUrl" /><br />

        <label for="newsInterval">News Intervall (in Sekunden)</label>
        <input
            type="number"
            id="newsInterval"
            v-model="config.newsInterval" /><br />

        <label for="departureInterval">Abfahrten Intervall (in Sekunden)</label>
        <input
            type="number"
            id="departureInterval"
            v-model="config.departureInterval" /><br />

        <label for="reloadInterval">Neulade Intervall (in Sekunden)</label>
        <input
            type="number"
            id="reloadInterval"
            v-model="config.reloadInterval" /><br />

        <input type="submit" value="Ändern" />
    </form>
    <br />

    <h3>Credits</h3>
    <p>
        Infoscreen &#169; Azubiwerk München<br />
        Version: {$ COMMIT_HASH $}<br />
        Entwickelt von
        <a href="https://friedinger.org/" target="_blank">Manuel Weis</a><br />
        Code:
        <a href="https://github.com/Friedinger/Infoscreen" target="_blank"
            >https://github.com/Friedinger/Infoscreen</a
        >
    </p>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import type { Config } from "./types/config";

const config = ref<Config>();
const newsToDelete = ref("");
const fileToUpload = ref<File | null>(null);
const message = ref("");

const apiEndpoint = "api.php";
const configEndpoint = "../config.json";

onMounted(() => {
    fetchConfig();
});

function fetchConfig() {
    fetch(`${configEndpoint}?t=${new Date().getTime()}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Configuration could not be loaded.");
            }
            return response.json();
        })
        .then((content: Config) => {
            config.value = content;
        })
        .catch((error: any) => {
            message.value = `Fehler: ${error.message}`;
        });
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        fileToUpload.value = target.files[0];
    }
}

function addNews() {
    if (!fileToUpload.value) {
        message.value = "Bitte eine Datei auswählen.";
        return;
    }

    const formData = new FormData();
    formData.append("file", fileToUpload.value);

    fetch(apiEndpoint, {
        method: "POST",
        body: formData,
    })
        .then((response) => {
            return response.json().then((result) => {
                if (!response.ok) {
                    throw new Error(
                        result.error || "Unknown error during upload."
                    );
                }
                return result;
            });
        })
        .then((result) => {
            message.value = `Erfolg: ${result.success}`;
            (
                document.querySelector('input[type="file"]') as HTMLInputElement
            ).value = "";
            fileToUpload.value = null;
            fetchConfig();
        })
        .catch((error: any) => {
            message.value = `Error: ${error.message}`;
        });
}

function deleteNews() {
    if (!newsToDelete.value) {
        message.value = "Bitte eine News zum Löschen auswählen.";
        return;
    }

    fetch(apiEndpoint, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ news: newsToDelete.value }),
    })
        .then((response) => {
            return response.json().then((result) => {
                if (!response.ok) {
                    throw new Error(
                        result.error || "Unbekannter Fehler beim Löschen."
                    );
                }
                return result;
            });
        })
        .then((result) => {
            message.value = `Erfolg: ${result.success}`;
            newsToDelete.value = "";
            fetchConfig();
        })
        .catch((error: any) => {
            message.value = `Fehler: ${error.message}`;
        });
}

function updateSettings() {
    if (!config.value) return;

    fetch(apiEndpoint, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(config.value),
    })
        .then((response) => {
            return response.json().then((result) => {
                if (!response.ok) {
                    throw new Error(
                        result.error || "Unknown error during saving."
                    );
                }
                return result;
            });
        })
        .then((result) => {
            message.value = `Success: ${result.success}`;
            fetchConfig();
        })
        .catch((error: any) => {
            message.value = `Error: ${error.message}`;
        });
}
</script>

<style>
.message {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f8f8f8;
    color: #333;
}
label {
    display: inline-block;
    min-width: 250px;
    margin-bottom: 5px;
}
input[type="url"] {
    width: 350px;
}
</style>
