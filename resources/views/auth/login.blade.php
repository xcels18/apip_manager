<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - APIP Manager</title>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    
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
    <style>
        body, input, button, select, textarea, .font-sans {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>
</head>
<body class="bg-surface-container-low font-sans text-body-md min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-indigo-600 flex items-center justify-center mb-4 shadow-lg shadow-primary/30 text-white transform transition-transform hover:rotate-12 cursor-pointer">
                <span class="material-symbols-outlined text-[32px]">admin_panel_settings</span>
            </div>
            <h1 class="text-headline-md font-extrabold text-on-surface tracking-tight text-center">APIP Manager</h1>
            <p class="text-body-sm text-on-surface-variant font-medium mt-1 text-center">Sistem Informasi Penugasan Inspektorat<br/>Kab. Puncak Jaya</p>
        </div>

        <!-- Card -->
        <div class="bg-surface rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-border-subtle p-8 w-full relative overflow-hidden">
            <!-- Decorative blur -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <h2 class="text-label-lg font-bold text-on-surface mb-6 text-center relative z-10">Masuk ke Akun Anda</h2>

            @if ($errors->any())
                <div class="mb-6 bg-error-container text-on-error-container p-4 rounded-xl text-body-sm font-medium flex items-start gap-3 border border-error/20 relative z-10 animate-[fadeIn_0.3s_ease-out]">
                    <span class="material-symbols-outlined text-[20px] shrink-0 text-error">error</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" class="space-y-5 relative z-10">
                @csrf
                
                <div>
                    <label for="email" class="block text-label-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-on-surface-variant/50">
                            <span class="material-symbols-outlined text-[20px]">mail</span>
                        </div>
                        <input type="email" name="email" id="email" class="w-full pl-11 pr-4 py-3.5 bg-surface-container-lowest border border-border-subtle rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface font-medium transition-all shadow-sm" value="{{ old('email') }}" placeholder="admin@inspektorat.go.id" required autofocus>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-label-sm font-bold text-on-surface-variant uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-on-surface-variant/50">
                            <span class="material-symbols-outlined text-[20px]">lock</span>
                        </div>
                        <input type="password" name="password" id="password" class="w-full pl-11 pr-4 py-3.5 bg-surface-container-lowest border border-border-subtle rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface font-medium transition-all shadow-sm" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember" class="w-5 h-5 rounded border-border-subtle text-primary focus:ring-primary focus:ring-offset-0 transition-all cursor-pointer">
                        <span class="text-body-sm font-medium text-on-surface-variant group-hover:text-on-surface transition-colors">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-indigo-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-primary/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2 mt-4 group">
                    <span>Masuk Sistem</span>
                    <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                </button>
            </form>
        </div>
        
        <p class="text-center text-[12px] text-on-surface-variant/60 mt-8 font-semibold uppercase tracking-wider">
            &copy; {{ date('Y') }} Inspektorat Kab. Puncak Jaya
        </p>
    </div>

</body>
</html>
