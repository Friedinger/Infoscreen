<template>
    <template v-if="config">
        <v-row>
            <v-col><h2>News Verwaltung</h2></v-col>
        </v-row>
        <v-row>
            <v-col cols="12" sm="10" md="8" lg="6" xl="4">
                <v-list>
                    <v-list-item
                        v-for="(news, index) in config.news"
                        :key="index"
                        :value="news"
                        rounded>
                        <template v-slot:append>
                            <v-btn
                                :href="`../news/${news}`"
                                variant="outlined"
                                class="mr-2">
                                Öffnen
                            </v-btn>
                            <v-btn
                                @click.stop="deleteNews(news)"
                                variant="outlined">
                                Löschen
                            </v-btn>
                        </template>
                        <v-list-item-title v-text="news" />
                    </v-list-item>
                    <v-divider />
                    <v-list-item rounded>
                        <template v-slot:prepend>
                            <v-dialog v-model="addNewsDialog" max-width="500">
                                <template v-slot:activator="{ props }">
                                    <v-btn v-bind="props" variant="outlined">
                                        News hinzufügen
                                    </v-btn>
                                </template>
                                <v-card>
                                    <v-card-title>News hinzufügen</v-card-title>
                                    <v-card-text>
                                        <v-file-input
                                            clearable
                                            label="News auswählen"
                                            variant="outlined"
                                            hide-details="auto"
                                            @change="handleFileChange" />
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-btn
                                            @click="addNews"
                                            color="primary"
                                            variant="outlined"
                                            >Hochladen</v-btn
                                        >
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </template>
                    </v-list-item>
                </v-list>
            </v-col>
        </v-row>
        <v-row>
            <v-col><h2>Einstellungen</h2></v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="6" lg="3">
                <v-text-field
                    label="Abfahrten API URL"
                    variant="outlined"
                    hide-details="auto"
                    v-model="config.departureUrl" />
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-number-input
                    label="News Intervall"
                    variant="outlined"
                    suffix="Sekunden"
                    hide-details="auto"
                    v-model="config.newsInterval" />
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-number-input
                    label="Abfahrten Intervall"
                    variant="outlined"
                    suffix="Sekunden"
                    hide-details="auto"
                    v-model="config.departureInterval" />
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-number-input
                    label="Neulade Intervall"
                    variant="outlined"
                    suffix="Sekunden"
                    hide-details="auto"
                    v-model="config.reloadInterval" />
            </v-col>
            <v-col cols="12" md="6" lg="3">
                <v-btn
                    @click="updateSettings"
                    variant="outlined"
                    color="primary"
                    >Speichern</v-btn
                >
            </v-col>
        </v-row>
    </template>
    <v-snackbar
        v-model="snackbar.open.value"
        timeout="2000"
        :color="snackbar.type.value">
        {{ snackbar.message }}
    </v-snackbar>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import { Config } from "../types/config";

enum SnackbarType {
    SUCCESS = "success",
    INFO = "info",
    WARNING = "warning",
    ERROR = "error",
}

const config = ref<Config>();
const fileToUpload = ref<File | null>(null);
const addNewsDialog = ref(false);
const snackbar = {
    open: ref<boolean>(false),
    message: ref<string>(""),
    type: ref<SnackbarType>(SnackbarType.INFO),
};

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
            openSnackbar(
                "Fehler beim Laden der Konfiguration.",
                SnackbarType.ERROR
            );
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
        openSnackbar("Bitte eine Datei auswählen.", SnackbarType.WARNING);
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
        .then(() => {
            openSnackbar("News erfolgreich hinzugefügt.", SnackbarType.SUCCESS);
            (
                document.querySelector('input[type="file"]') as HTMLInputElement
            ).value = "";
            fileToUpload.value = null;
            addNewsDialog.value = false;
            fetchConfig();
        })
        .catch(() => {
            openSnackbar("Fehler beim Hochladen der News.", SnackbarType.ERROR);
        });
}

function deleteNews(news: string) {
    fetch(apiEndpoint, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ news: news }),
    })
        .then((response) => {
            return response.json().then((result) => {
                if (!response.ok) {
                    throw new Error(
                        result.error || "Unknown error during deletion."
                    );
                }
                return result;
            });
        })
        .then(() => {
            openSnackbar("News erfolgreich gelöscht.", SnackbarType.SUCCESS);
            fetchConfig();
        })
        .catch(() => {
            openSnackbar("Fehler beim Löschen der News.", SnackbarType.ERROR);
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
        .then(() => {
            openSnackbar(
                "Einstellungen erfolgreich gespeichert.",
                SnackbarType.SUCCESS
            );
            fetchConfig();
        })
        .catch(() => {
            openSnackbar(
                "Fehler beim Speichern der Einstellungen.",
                SnackbarType.ERROR
            );
        });
}

function openSnackbar(message: string, type: SnackbarType = SnackbarType.INFO) {
    snackbar.message.value = message;
    snackbar.type.value = type;
    snackbar.open.value = true;
}
</script>
