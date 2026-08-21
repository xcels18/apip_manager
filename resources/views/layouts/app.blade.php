<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'APIP Manager') - Inspektorat Puncak Jaya</title>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS & Config -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-highest": "#E2E8F0",
                        "on-secondary-fixed": "#0F172A",
                        "surface": "#FFFFFF",
                        "surface-card": "#FFFFFF",
                        "inverse-primary": "#F1F5F9",
                        "surface-container-low": "#F8FAFC",
                        "on-tertiary-fixed-variant": "#0F172A",
                        "on-tertiary-container": "#0F172A",
                        "tertiary-fixed-dim": "#CBD5E1",
                        "primary": "#0F172A",
                        "secondary-fixed": "#E2E8F0",
                        "secondary": "#475569",
                        "inverse-on-surface": "#F1F5F9",
                        "status-success": "#10B981",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container": "#F1F5F9",
                        "sidebar-bg": "#FFFFFF",
                        "surface-tint": "#0F172A",
                        "primary-container": "#F1F5F9",
                        "surface-container-high": "#E2E8F0",
                        "on-primary-container": "#0F172A",
                        "tertiary-container": "#F1F5F9",
                        "outline-variant": "#CBD5E1",
                        "on-primary": "#FFFFFF",
                        "secondary-container": "#F8FAFC",
                        "on-secondary": "#FFFFFF",
                        "primary-fixed-dim": "#94A3B8",
                        "outline": "#94A3B8",
                        "primary-fixed": "#E2E8F0",
                        "border-subtle": "#E2E8F0",
                        "surface-bright": "#FFFFFF",
                        "surface-dim": "#F1F5F9",
                        "tertiary-fixed": "#F8FAFC",
                        "secondary-fixed-dim": "#CBD5E1",
                        "on-primary-fixed-variant": "#0F172A",
                        "background": "#F8FAFC",
                        "on-surface-variant": "#64748B",
                        "on-primary-fixed": "#0F172A",
                        "on-tertiary": "#FFFFFF",
                        "on-error": "#FFFFFF",
                        "inverse-surface": "#0F172A",
                        "on-error-container": "#7F1D1D",
                        "surface-variant": "#F1F5F9",
                        "error-container": "#FEE2E2",
                        "on-secondary-container": "#0F172A",
                        "surface-main": "#F8FAFC",
                        "tertiary": "#0F172A",
                        "on-tertiary-fixed": "#0F172A",
                        "error": "#EF4444",
                        "on-surface": "#0F172A",
                        "on-secondary-fixed-variant": "#0F172A",
                        "on-background": "#0F172A",
                        "status-error": "#EF4444"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.375rem",
                        "md": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "2xl": "1.5rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "stack-lg": "24px",
                        "section-gap": "32px",
                        "gutter": "20px",
                        "stack-sm": "8px",
                        "margin-tablet": "32px",
                        "margin-desktop": "48px",
                        "stack-md": "16px",
                        "sidebar-width": "260px"
                    },
                    "fontFamily": {
                        "sans": ["'Plus Jakarta Sans'", "sans-serif"],
                        "body-lg": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-md": ["'Plus Jakarta Sans'", "sans-serif"],
                        "body-sm": ["'Plus Jakarta Sans'", "sans-serif"],
                        "body-md": ["'Plus Jakarta Sans'", "sans-serif"],
                        "label-sm": ["'Plus Jakarta Sans'", "sans-serif"],
                        "label-lg": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-xl": ["'Plus Jakarta Sans'", "sans-serif"],
                        "headline-lg": ["'Plus Jakarta Sans'", "sans-serif"],
                        "label-md": ["'Plus Jakarta Sans'", "sans-serif"]
                    },
                    "fontSize": {
                        "body-sm": ["0.8125rem", {"lineHeight": "1.25rem", "fontWeight": "400"}],
                        "body-md": ["0.875rem", {"lineHeight": "1.375rem", "fontWeight": "400"}],
                        "body-lg": ["0.9375rem", {"lineHeight": "1.5rem", "fontWeight": "400"}],
                        "label-sm": ["0.6875rem", {"lineHeight": "1rem", "letterSpacing": "0.03em", "fontWeight": "600"}],
                        "label-md": ["0.75rem", {"lineHeight": "1.125rem", "fontWeight": "600"}],
                        "label-lg": ["0.875rem", {"lineHeight": "1.25rem", "fontWeight": "600"}],
                        "headline-sm": ["1.125rem", {"lineHeight": "1.5rem", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                        "headline-md": ["1.25rem", {"lineHeight": "1.75rem", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                        "headline-lg": ["1.5rem", {"lineHeight": "2rem", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                        "headline-xl": ["1.875rem", {"lineHeight": "2.25rem", "letterSpacing": "-0.02em", "fontWeight": "800"}]
                    }
                }
            }
        }
    </script>
    @stack('styles')
    <style>
        body, input, button, select, textarea, .font-sans {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>
</head>
<body class="text-on-surface bg-background font-sans text-body-md min-h-screen antialiased">
    
    @include('layouts.sidebar')

    @include('layouts.topbar')

    <!-- MAIN CONTENT AREA (240px sidebar + 32px gap = 272px) -->
    <main class="pt-8 pb-16 px-4 md:pl-[272px] md:pr-margin-desktop min-h-[calc(100vh-64px)] w-full flex flex-col h-full">
        <div class="max-w-[1200px] w-full flex flex-col h-full">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
