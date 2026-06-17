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
            fontFamily: {
                sans: ['"Hind Siliguri"', ...defaultTheme.fontFamily.sans],
                display: ['"Baloo 2"', '"Hind Siliguri"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    green: '#1f8a52',   // primary
                    leaf: '#3aa76d',
                    orange: '#f0883e',  // accent / CTA
                    cream: '#fbf6ec',   // app background
                    sand: '#efe4d2',    // borders / soft fills
                },
            },
            boxShadow: {
                soft: '0 6px 20px -8px rgba(31, 138, 82, 0.18)',
            },
        },
    },

    plugins: [forms],
};
