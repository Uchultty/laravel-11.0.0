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
                // "brand" = warna aksen/tema utama (biru). Dipakai di tombol
                // primary, link, focus ring input, dan gradient header.
                brand: {
                    50: "#eef9ff", // background sangat muda (misal alert/badge lembut)
                    100: "#d8f1ff", // background muda
                    200: "#b7e8ff", // border/hover halus
                    300: "#83daff", // aksen sedang, jarang dipakai langsung
                    400: "#47c5ff", // aksen lebih terang
                    500: "#1ca6f8", // warna dasar brand: focus ring input, border logo
                    600: "#0b86dc", // warna utama tombol/link (ui-btn-primary, ui-link)
                    700: "#0c6ab2", // warna hover tombol/link (lebih gelap dari 600)
                    800: "#115992", // aksen gelap, jarang dipakai
                    900: "#154b79", // paling gelap, biasanya untuk teks di atas background terang
                },
                // "ink" = warna netral abu-abu, dipakai untuk teks, border,
                // dan background kartu/tabel di hampir semua halaman.
                ink: {
                    50: "#f8fafc", // background sangat muda (header tabel, baris hover)
                    100: "#f1f5f9", // background muda (badge, baris genap tabel)
                    200: "#e2e8f0", // border tipis (card, input, divider)
                    300: "#cbd5e1", // border input, scrollbar
                    400: "#94a3b8", // teks placeholder / ikon nonaktif
                    500: "#64748b", // teks sekunder (label kecil, help text)
                    600: "#475569", // teks judul kolom tabel (th)
                    700: "#334155", // teks isi tabel/label form (lebih gelap)
                    800: "#1e293b", // teks judul section
                    900: "#0f172a", // teks judul utama/heading halaman (paling gelap)
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
