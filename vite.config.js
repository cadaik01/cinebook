import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/header.css",
                "resources/css/footer.css",
                "resources/css/homepage.css",
                "resources/css/movie_details.css",
                "resources/css/now_showing.css",
                "resources/css/upcoming_movies.css",
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
