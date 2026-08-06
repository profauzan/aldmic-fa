module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './public/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#101114',
                paper: '#f1eee8',
                muted: '#9b9a98',
                amber: '#e7a74e',
                surface: '#191a1e',
            },
            fontFamily: {
                display: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['DM Mono', 'ui-monospace', 'SFMono-Regular', 'monospace'],
            },
            keyframes: {
                rise: { '0%': { opacity: '0', transform: 'translateY(14px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                shimmer: { '100%': { transform: 'translateX(100%)' } },
                pulseSoft: { '0%, 100%': { opacity: '0.35' }, '50%': { opacity: '1' } },
            },
            animation: {
                rise: 'rise 650ms cubic-bezier(0.22, 1, 0.36, 1) both',
                shimmer: 'shimmer 1.6s infinite',
                pulseSoft: 'pulseSoft 1.2s ease-in-out infinite',
            },
        },
    },
    plugins: [],
};
