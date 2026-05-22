import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: "#eef9ff",
                    100: "#d8f1ff",
                    200: "#b7e8ff",
                    300: "#83daff",
                    400: "#47c5ff",
                    500: "#1ca6f8",
                    600: "#0b86dc",
                    700: "#0c6ab2",
                    800: "#115992",
                    900: "#154b79",
                },
                ink: {
                    50: "#f8fafc",
                    100: "#f1f5f9",
                    200: "#e2e8f0",
                    300: "#cbd5e1",
                    400: "#94a3b8",
                    500: "#64748b",
                    600: "#475569",
                    700: "#334155",
                    800: "#1e293b",
                    900: "#0f172a",
                },
            },
            borderRadius: {
                xl2: "1.125rem",
            },
            boxShadow: {
                panel: "0 10px 25px -12px rgba(2, 8, 23, 0.22)",
            },
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
