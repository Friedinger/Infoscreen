import { defineConfig } from "vite";
import { fileURLToPath, URL } from "node:url";
import { viteSingleFile } from "vite-plugin-singlefile";
import vue from "@vitejs/plugin-vue";
import vuetify from "vite-plugin-vuetify";

export default defineConfig(({ mode }) => ({
    plugins: [
        vue(),
        vuetify({ autoImport: true }),
        mode === "development" ? null : viteSingleFile(),
    ],
    root: "pages",
    publicDir: "../public",
    build: {
        rollupOptions: {
            input: inputs(mode),
        },
        outDir: "../dist",
        emptyOutDir: mode === "development",
        chunkSizeWarningLimit: 1000,
    },
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
        },
    },
}));

function inputs(mode: string) {
    console.log("mode:", mode);
    return {
        ...(mode === "development" || mode === "index"
            ? { index: "pages/index.html" }
            : {}),
        ...(mode === "development" || mode === "admin"
            ? { admin: "pages/admin/index.html" }
            : {}),
    };
}
