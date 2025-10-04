import { defineConfig } from "vite";
import { viteSingleFile } from "vite-plugin-singlefile";
import { viteStaticCopy } from "vite-plugin-static-copy";

const devCopyTargets = ["*", "!**/*sample*"];

const buildCopyTargets = [
    "!**/*.html",
    "!**/*.js",
    "!**/*.css",
    ...devCopyTargets,
];

export default defineConfig(({ mode }) => ({
    plugins: [
        mode === "development" ? null : viteSingleFile(),
        viteStaticCopy({
            targets: [
                {
                    src:
                        mode === "development"
                            ? devCopyTargets
                            : buildCopyTargets,
                    dest: "",
                },
            ],
        }),
    ],
    root: "src",
    build: {
        outDir: "../dist",
        emptyOutDir: true,
    },
}));
