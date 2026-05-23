import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#004a99',
                    hover: '#003d80',
                    light: 'rgba(0, 74, 153, 0.1)',
                },
                surface: '#f8fafc',
                'border-soft': '#f1f5f9',
                'text-main': '#0f172a',
                'text-muted': '#64748b',
                // Adding a softer blue for secondary elements
                'blue-soft': '#eff6ff', 
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                outfit: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                'card': '2.5rem',
            }
        },
    },

    plugins: [forms],
};
