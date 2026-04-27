/**
 * Shared Tailwind CDN theme extension (load after tailwindcss.com script).
 */
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      colors: {
        gray: {
          50: '#F9F9FB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#4B5563',
          800: '#1F2937',
          900: '#111827',
        },
        /** Brand primary — 600 = #2563eb (StuNest). Matches tailwind.config.cjs. */
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
          950: '#172554',
        },
      },
    },
  },
};
