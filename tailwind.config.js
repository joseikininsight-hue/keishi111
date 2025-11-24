/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './template-parts/**/*.php',
    './pages/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#059669',
          dark: '#047857',
          darker: '#065f46',
          light: '#10b981',
        },
        secondary: {
          DEFAULT: '#000000',
          light: '#1a1a1a',
          dark: '#000000',
        },
        accent: {
          DEFAULT: '#ffeb3b',
          light: '#fff9c4',
          dark: '#fbc02d',
        },
      },
      fontFamily: {
        'inter': ['Inter', 'sans-serif'],
        'outfit': ['Outfit', 'sans-serif'],
        'space': ['Space Grotesk', 'sans-serif'],
        'noto': ['Noto Sans JP', 'sans-serif'],
      },
      fontSize: {
        'h1': 'clamp(28px, 4vw, 36px)',
        'h2': 'clamp(22px, 3vw, 28px)',
        'h3': 'clamp(18px, 2.5vw, 22px)',
        'h4': '18px',
        'h5': '16px',
        'h6': '14px',
      },
      spacing: {
        '0': '0',
        '1': '4px',
        '2': '8px',
        '3': '12px',
        '4': '16px',
        '5': '20px',
        '6': '24px',
        '8': '32px',
        '10': '40px',
        '12': '48px',
        '16': '64px',
        '20': '80px',
        '24': '96px',
        '32': '128px',
      },
      borderRadius: {
        'sm': '4px',
        'DEFAULT': '8px',
        'md': '12px',
        'lg': '16px',
        'xl': '20px',
        '2xl': '24px',
        'full': '9999px',
      },
      boxShadow: {
        'sm': '0 1px 2px rgba(0, 0, 0, 0.05)',
        'DEFAULT': '0 2px 8px rgba(0, 0, 0, 0.08)',
        'md': '0 4px 12px rgba(0, 0, 0, 0.1)',
        'lg': '0 8px 24px rgba(0, 0, 0, 0.12)',
        'xl': '0 12px 32px rgba(0, 0, 0, 0.15)',
      },
      transitionDuration: {
        'DEFAULT': '200ms',
        'fast': '150ms',
        'slow': '300ms',
      },
    },
  },
  plugins: [],
}
