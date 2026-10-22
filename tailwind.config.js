import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // 🔥 INI YANG HILANG — tanpa ini dark: tidak merespons class
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        screens: {
            sm:   '640px',
            md:   '768px',
            lg:   '1024px',
            xl:   '1280px',
            '2xl':'1536px',
            '3xl':'1920px',
        },
        extend: {
            // 🔥 COPY DARI CDN CONFIG — warna custom
            colors: {
                midnight: '#050A30',
                deepblue: '#0B1F6B',
                electric: '#2563EB',
                cyan:     '#00D9FF',
                purple:   '#7C3AED',
                violet:   '#9333EA',
                unicode: {
                    50:  '#f0f4ff',
                    100: '#dbe4ff',
                    200: '#baceff',
                    300: '#8fb2ff',
                    400: '#5e8fff',
                    500: '#2563EB',
                    600: '#1a4dc4',
                    700: '#123a96',
                    800: '#0d2a6e',
                    900: '#050A30',
                },
            },
            fontFamily: {
                sans:     ['Inter', 'system-ui', 'sans-serif'],
                poppins:  ['Poppins', 'sans-serif'],
            },
            boxShadow: {
                glow:     '0 0 8px 2px rgba(255, 255, 0, 0.6)',
                'glow-lg':'0 0 20px 4px rgba(255, 255, 0, 0.8)',
            },
            animation: {
                marquee:      'marquee 25s linear infinite',
                float:        'float 3s ease-in-out infinite',
                'pulse-slow': 'pulse 3s ease-in-out infinite',
                'gradient-x': 'gradient-x 15s ease infinite',
                'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
            },
            keyframes: {
                marquee: {
                    '0%':   { transform: 'translateX(100%)' },
                    '100%': { transform: 'translateX(-100%)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%':      { transform: 'translateY(-12px)' },
                },
                'gradient-x': {
                    '0%, 100%': { 'background-position': '0% 50%' },
                    '50%':      { 'background-position': '100% 50%' },
                },
                fadeInUp: {
                    '0%':   { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [forms],
};