<template>
    <v-app>
        <v-app-bar color="primary">
            <v-app-bar-title>Infoscreen Admin</v-app-bar-title>
            <template v-slot:append>
                <v-btn href="../" variant="tonal" class="mr-4">
                    Zurück zum Infoscreen
                </v-btn>
            </template>
        </v-app-bar>
        <v-main>
            <v-container>
                <v-row>
                    <v-col><h1>Infoscreen Admin</h1></v-col>
                </v-row>
                <v-row>
                    <v-col><AdminForm /></v-col>
                </v-row>
            </v-container>
        </v-main>
        <v-container>
            <v-footer color="primary" class="text-center" rounded elevation="2">
                <v-col>Infoscreen &#169; Azubiwerk München</v-col>
                <v-col>Version: {$COMMIT_HASH$}</v-col>
                <v-col>
                    Entwickelt von
                    <a href="https://friedinger.org/" target="_blank"
                        >Manuel Weis</a
                    >
                </v-col>
                <v-col>
                    Code:
                    <a
                        href="https://github.com/Friedinger/Infoscreen"
                        target="_blank"
                        >https://github.com/Friedinger/Infoscreen</a
                    >
                </v-col>
            </v-footer>
        </v-container>
    </v-app>
</template>

<script setup lang="ts">
import AdminForm from "./components/adminForm.vue";
</script>
