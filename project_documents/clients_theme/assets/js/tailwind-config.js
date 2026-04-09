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
      },
    },
  },
};
