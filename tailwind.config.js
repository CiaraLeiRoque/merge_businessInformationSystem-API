import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat','Figtree', ...defaultTheme.fontFamily.sans],

            },
            colors: {
                'business-website-header': '#667984',
                'website-main': '#1a202c',
                'website-main1': '#1A518A',
            }
        },
    },

    plugins: [forms],
};
