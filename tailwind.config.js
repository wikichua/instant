const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    darkMode: 'class',

    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/vues/**/*.vue',
        'node_modules/vue-tailwind/dist/*.js'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    variants: {
        extend: {
            tableLayout: ['hover', 'focus'],
            opacity: ['disabled'],
            textColor: ['visited'],
            backgroundColor: ['checked'],
            borderColor: ['checked'],
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
