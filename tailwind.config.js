import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/*
 * Design tokens — "Violet Sunrise"
 *
 * Semantic colors (bg, surface, ink, muted, line, ...) read CSS variables
 * defined in resources/css/app.css, so the same class works in light and dark
 * mode without a `dark:` variant. Brand/accent scales are static.
 */
const token = (name) => `rgb(var(--c-${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans Variable"', '"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Semantic (theme-aware)
                bg: token('bg'),
                surface: token('surface'),
                'surface-2': token('surface-2'),
                ink: token('ink'),
                muted: token('muted'),
                line: token('line'),
                'brand-soft': token('brand-soft'),
                'brand-text': token('brand-text'),

                // Brand: violet
                brand: {
                    50: '#F5F0FF',
                    100: '#EBE0FF',
                    200: '#D8C5FF',
                    300: '#C4B5FD',
                    400: '#A78BFA',
                    500: '#8B5CF6',
                    600: '#7C3AED',
                    700: '#6D28D9',
                    800: '#5B21B6',
                    900: '#3B1480',
                },
                // Accents
                pink: {
                    50: '#FFF0F6',
                    100: '#FFDCEA',
                    300: '#F9A8D4',
                    400: '#FF7AA8',
                    500: '#FF5C8A',
                    600: '#DB2777',
                    700: '#BE185D',
                },
                sun: {
                    50: '#FFF8E1',
                    100: '#FFEFB8',
                    300: '#FFD966',
                    400: '#FFC83D',
                    500: '#F5B100',
                    700: '#92400E',
                },
                mint: {
                    50: '#E6FAF7',
                    100: '#C6F3EC',
                    300: '#5EEAD4',
                    400: '#2DD4BF',
                    500: '#14B8A6',
                    800: '#0F766E',
                },
                sky: {
                    50: '#E6F9FD',
                    100: '#C2F0FA',
                    300: '#67DFF5',
                    400: '#22C7E8',
                    500: '#06B6D4',
                    800: '#0E7490',
                },
                danger: {
                    50: '#FFF0F0',
                    100: '#FFDADA',
                    500: '#EF4444',
                    600: '#DC2626',
                    700: '#B42318',
                },
            },

            fontSize: {
                // Smallest allowed size across the site is 12px.
                caption: ['0.75rem', { lineHeight: '1rem' }],
                small: ['0.875rem', { lineHeight: '1.25rem' }],
                body: ['1rem', { lineHeight: '1.625rem' }],
            },

            borderRadius: {
                sm: '8px',
                md: '12px',
                lg: '16px',
                xl: '24px',
            },

            boxShadow: {
                soft: '0 1px 2px rgb(23 18 58 / 0.04), 0 4px 16px -4px rgb(23 18 58 / 0.08)',
                card: '0 1px 2px rgb(23 18 58 / 0.04), 0 12px 32px -12px rgb(124 58 237 / 0.18)',
                pop: '0 20px 48px -16px rgb(124 58 237 / 0.35)',
                glow: '0 8px 24px -8px rgb(124 58 237 / 0.55)',
            },

            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, #7C3AED 0%, #A855F7 50%, #FF5C8A 100%)',
                'brand-gradient-soft':
                    'linear-gradient(135deg, rgb(124 58 237 / 0.10) 0%, rgb(255 92 138 / 0.10) 100%)',
                'sunrise': 'linear-gradient(135deg, #FFC83D 0%, #FF5C8A 100%)',
            },

            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                marquee: {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-8px)' },
                },
            },
            animation: {
                'fade-up': 'fade-up .4s ease-out both',
                marquee: 'marquee 30s linear infinite',
                float: 'float 5s ease-in-out infinite',
            },

            maxWidth: {
                page: '80rem',
            },
        },
    },

    plugins: [forms],
};
