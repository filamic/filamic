<html lang="en" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Required</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Check for theme in localStorage or default to system preference
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body
    class="relative flex min-h-screen items-center justify-center bg-gray-100 p-6 text-gray-600 transition-colors duration-300 dark:bg-[#09090b] dark:text-gray-400">

    <!-- Theme Toggle -->
    <div class="absolute top-6 right-6 z-50">
        <button onclick="toggleTheme()"
            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white shadow-sm transition-colors hover:bg-gray-50 dark:border-white/10 dark:bg-[#18181b] dark:hover:bg-[#202024]">
            <x-heroicon-o-sun class="h-5 w-5 dark:hidden" />
            <x-heroicon-o-moon class="hidden h-5 w-5 dark:block" />
        </button>
    </div>

    <!-- Card Container -->
    <div
        class="relative w-full max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl shadow-gray-200/40 dark:border-white/10 dark:bg-[#121214] dark:shadow-black/40 md:grid md:grid-cols-2">

        <!-- Left Side: Content -->
        <div class="flex flex-col justify-between p-8 sm:p-10 md:p-12">
            <div>
                <!-- Status Badge -->
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-laravel-100 bg-laravel-50 px-3 py-1 text-xs font-medium text-laravel-600 dark:border-laravel-900/30 dark:bg-laravel-900/10 dark:text-laravel-500">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-laravel-500 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-laravel-500"></span>
                    </span>
                    Action Required
                </div>

                <h1 class="mb-3 text-2xl font-medium tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Pending Configuration
                </h1>

                <p class="mb-10 text-base leading-relaxed text-gray-500 dark:text-gray-400">
                    This application requires administrator verification before it can be accessed. Please complete the
                    setup steps below.
                </p>

                <!-- Steps List -->
                <div class="relative pl-2">
                    <!-- Vertical Connector Line -->
                    <div class="absolute left-[19px] top-3 bottom-8 w-px bg-gray-200 dark:bg-white/10"></div>

                    <!-- Step 1 -->
                    <div class="group relative mb-8 flex items-start gap-6">
                        <div
                            class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white ring-4 ring-white transition-colors group-hover:border-laravel-500/30 dark:border-white/10 dark:bg-[#18181b] dark:ring-[#121214]">
                            <x-heroicon-c-shield-exclamation class="h-5 w-5 text-laravel-500" />
                        </div>
                        <div class="pt-2">
                            <p class="text-base font-medium text-gray-900 dark:text-gray-200">
                                Authenticate as Administrator
                            </p>
                            <a href="{{ route('filament.admin.auth.login') }}"
                                class="mt-1 inline-flex items-center gap-1 text-sm text-laravel-600 transition-colors hover:text-laravel-500 dark:text-laravel-500">
                                Open Admin Panel <x-heroicon-c-arrow-up-right class="h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="group relative flex items-start gap-6">
                        <div
                            class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white ring-4 ring-white transition-colors group-hover:border-gray-300 dark:border-white/10 dark:bg-[#18181b] dark:ring-[#121214]">
                            <x-heroicon-c-cog-6-tooth class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                        </div>
                        <div class="pt-2">
                            <p class="text-base font-medium text-gray-900 dark:text-gray-200">
                                Configure System Environment
                            </p>
                            <span class="mt-1 block text-sm text-gray-500 dark:text-gray-500">
                                Waiting for authorization...
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="mt-10">
                @php
                    $returnTo = request('return_to', url()->previous());
                    $returnUrl = filter_var($returnTo, FILTER_VALIDATE_URL) && parse_url($returnTo, PHP_URL_HOST) === request()->getHost()
                        ? $returnTo
                        : url()->previous();
                @endphp
                <a href="{{ $returnUrl }}"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-6 py-3 text-sm font-medium text-white transition-all hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 dark:bg-white dark:text-black dark:hover:bg-gray-100 dark:focus:ring-white dark:focus:ring-offset-[#121214]">
                    Retry Connection
                </a>
            </div>
        </div>

        <!-- Right Side: Visuals (Abstract Wireframe Grid) -->
        <div class="relative hidden min-h-[400px] overflow-hidden bg-[#0F0F11] md:block">
            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-linear-to-br from-[#1a1a1e] to-black"></div>

            <!-- Red/Orange Glow -->
            <div class="absolute -right-20 -top-20 h-[500px] w-[500px] rounded-full bg-laravel-500/20 blur-[100px]">
            </div>
            <div class="absolute -bottom-20 -left-20 h-[400px] w-[400px] rounded-full bg-orange-600/10 blur-[80px]">
            </div>

            <!-- Geometric Grid Pattern (CSS Only) -->
            <div class="absolute inset-0"
                style="background-image: linear-gradient(rgba(255, 45, 32, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 45, 32, 0.1) 1px, transparent 1px); background-size: 40px 40px; mask-image: radial-gradient(circle at center, black 40%, transparent 100%); -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 100%);">
            </div>

            <!-- Abstract Isometric Shapes -->
            <div class="absolute inset-0 flex items-center justify-center">
                <!-- Main Box Wireframe -->
                <div
                    class="relative h-64 w-64 rotate-45 transform border border-laravel-500/30 bg-transparent transition-transform duration-700 hover:rotate-60 hover:scale-105">
                    <div class="absolute inset-0 border border-laravel-500/20"
                        style="transform: translate(10px, -10px);"></div>
                    <div class="absolute inset-0 border border-laravel-500/10"
                        style="transform: translate(20px, -20px);"></div>
                    <div class="absolute inset-0 border border-laravel-500/5"
                        style="transform: translate(30px, -30px);"></div>

                    <!-- Inner Grid Lines -->
                    <div class="absolute inset-0 grid grid-cols-2 grid-rows-2">
                        <div class="border-b border-r border-laravel-500/20"></div>
                        <div class="border-b border-laravel-500/20"></div>
                        <div class="border-r border-laravel-500/20"></div>
                    </div>
                </div>
            </div>

            <!-- Bottom Fade -->
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-linear-to-t from-[#0F0F11] to-transparent"></div>
        </div>
    </div>
</body>

</html>