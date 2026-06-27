import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#006b3f',
                'primary-dark': '#004d2c',
                'primary-light': '#e8f5ee',
                'surface': 'var(--color-surface)',
                'on-surface': 'var(--color-on-surface)',
                'text-muted': 'var(--color-text-muted)',
                'success-green': '#28A745',
                'error-red': '#DC3545',
                'warning-amber': '#FFC107',
                'info-blue': '#17A2B8',
                slate: {
                    50: '#eaf5f0',
                    100: '#d1e8df',
                    200: '#9bc2b3',
                    300: '#6da38e',
                    400: '#4b856f',
                    500: '#336b55',
                    600: '#265241',
                    700: '#1b3d30',
                    800: '#132c22',
                    900: '#0a1c14',
                    950: '#05110c',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
