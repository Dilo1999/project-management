/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/js/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'sans-serif'],
            },
            boxShadow: {
                'neu-flat': '10px 10px 20px #a3b1c6, -10px -10px 20px #ffffff',
                'neu-inset': 'inset 6px 6px 10px #a3b1c6, inset -6px -6px 10px #ffffff',
                'neu-inset-sm': 'inset 6px 6px 10px rgba(163,177,198,0.5), inset -6px -6px 10px rgba(255,255,255,0.5)',
                'neu-btn': '5px 5px 15px #a3b1c6, -5px -5px 15px #ffffff',
            },
        },
    },
    plugins: [],
};

