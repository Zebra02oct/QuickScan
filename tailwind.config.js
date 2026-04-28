import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    // 🔥 Tambahkan baris ini untuk mematikan dark mode otomatis bray!
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', "sans-serif"],
            },

            // 🔥 Gabungkan kedua animasi di sini agar tidak saling menimpa
            animation: {
                "bounce-slow": "bounce-slow 4s ease-in-out infinite",
                jiggle: "jiggle 0.4s ease-in-out",
            },

            keyframes: {
                "bounce-slow": {
                    "0%, 100%": { transform: "translateY(0)" },
                    "50%": { transform: "translateY(-10px)" },
                },
                jiggle: {
                    "0%, 100%": { transform: "rotate(0) scale(1)" },
                    "25%": { transform: "rotate(-10deg) scale(1.1)" },
                    "75%": { transform: "rotate(10deg) scale(1.1)" },
                },
            },
        },
    },

    plugins: [forms],
};
