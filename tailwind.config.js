import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Background colors
        'bg-purple-50', 'bg-purple-100', 'bg-purple-600', 'bg-purple-700',
        'bg-blue-50', 'bg-blue-100', 'bg-blue-600', 'bg-blue-700', 'bg-blue-900',
        'bg-green-50', 'bg-green-100', 'bg-green-600', 'bg-green-700', 'bg-green-800', 'bg-green-900',
        'bg-red-50', 'bg-red-100', 'bg-red-600', 'bg-red-700', 'bg-red-800', 'bg-red-900',
        'bg-yellow-50', 'bg-yellow-100', 'bg-yellow-600', 'bg-yellow-700', 'bg-yellow-900',
        'bg-orange-50', 'bg-orange-100', 'bg-orange-600', 'bg-orange-700', 'bg-orange-900',
        'bg-gray-50', 'bg-gray-100', 'bg-gray-200', 'bg-gray-300', 'bg-gray-500', 'bg-gray-700', 'bg-gray-900',
        
        // Text colors
        'text-purple-200', 'text-purple-600', 'text-purple-700', 'text-purple-800', 'text-purple-900',
        'text-blue-600', 'text-blue-700', 'text-blue-800', 'text-blue-900',
        'text-green-600', 'text-green-700', 'text-green-800', 'text-green-900',
        'text-red-600', 'text-red-700', 'text-red-800', 'text-red-900',
        'text-yellow-600', 'text-yellow-700', 'text-yellow-800', 'text-yellow-900',
        'text-orange-600', 'text-orange-700', 'text-orange-800', 'text-orange-900',
        'text-gray-500', 'text-gray-600', 'text-gray-700', 'text-gray-800', 'text-gray-900',
        
        // Border colors
        'border-purple-100', 'border-purple-200', 'border-purple-600',
        'border-blue-100', 'border-blue-200', 'border-blue-600',
        'border-green-100', 'border-green-200', 'border-green-600',
        'border-red-100', 'border-red-200', 'border-red-600',
        'border-yellow-100', 'border-yellow-200', 'border-yellow-600',
        'border-orange-100', 'border-orange-200', 'border-orange-600',
        'border-gray-100', 'border-gray-200', 'border-gray-300',
        
        // Gradients
        'from-purple-600', 'to-purple-700', 'from-purple-700', 'to-purple-800',
        'from-blue-600', 'to-blue-700', 'from-blue-700', 'to-blue-800',
        'from-green-600', 'to-green-700', 'from-green-700', 'to-green-800',
        'from-red-600', 'to-red-700',
        'from-yellow-600', 'to-yellow-700',
        'from-orange-600', 'to-orange-700',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
