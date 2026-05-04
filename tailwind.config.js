import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue", // (Opsional jika ke depan mau pakai Vue)
  ],
  theme: {
    extend: {},
  },
  plugins: [],

};
