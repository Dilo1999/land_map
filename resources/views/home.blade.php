<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dambulla Plan 2001MT65 — Interactive Plan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased bg-white text-gray-900">
    {{-- Mirrors src/app/App.tsx: size-full flex; min-h-screen fills viewport when html/body are h-full --}}
    <div
        class="size-full min-h-screen flex flex-col lg:flex-row min-h-0"
        data-plan-root
        data-svg-url="{{ asset('images/dambulla-plan.svg') }}"
    >
        {{-- Left Panel - Map (make larger) --}}
        <div class="w-full lg:w-3/4 bg-white flex items-center justify-center p-4 sm:p-6 lg:p-8 min-w-0 relative min-h-[50vh] lg:min-h-0">
            {{-- Zoomable viewport (only map zooms, not the whole page) --}}
            <div data-map-viewport class="w-full h-full overflow-auto relative overscroll-contain touch-pan-x touch-pan-y rounded-lg">
                <div data-map-content class="origin-top-left">
                    {{-- InteractiveMap: div w-full h-full --}}
                    <div data-map-mount class="w-full h-full min-h-0"></div>
                </div>
            </div>

            {{-- Zoom + actions --}}
            <div class="absolute left-3 top-3 sm:left-5 sm:top-5 lg:left-10 lg:top-10 z-10">
                <div class="rounded-2xl border border-black/10 bg-white/90 backdrop-blur shadow-lg shadow-black/10 p-1.5">
                    <div class="flex flex-col gap-1.5">
                        <div class="flex flex-col gap-1.5">
                            <button
                                type="button"
                                data-zoom-in
                                class="w-9 h-9 rounded-full border-2 border-gray-900 bg-white flex items-center justify-center hover:bg-gray-100 active:scale-[0.98] transition"
                                aria-label="Zoom in"
                            >
                                <span class="text-lg leading-none">+</span>
                            </button>
                            <button
                                type="button"
                                data-zoom-out
                                class="w-9 h-9 rounded-full border-2 border-gray-900 bg-white flex items-center justify-center hover:bg-gray-100 active:scale-[0.98] transition"
                                aria-label="Zoom out"
                            >
                                <span class="text-lg leading-none">−</span>
                            </button>
                            <button
                                type="button"
                                data-zoom-reset
                                class="w-9 h-9 rounded-full border-2 border-gray-900 bg-white flex items-center justify-center hover:bg-gray-100 active:scale-[0.98] transition"
                                aria-label="Reset zoom"
                            >
                                <span class="text-xs font-semibold tracking-wide">1:1</span>
                            </button>
                        </div>

                        <div class="h-px bg-black/10"></div>

                        <button
                            type="button"
                            data-view-details
                            class="w-full px-3 py-2 rounded-full border-2 border-gray-900 bg-gray-900 text-white hover:bg-gray-800 active:scale-[0.99] transition text-sm font-semibold"
                        >
                            View
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel - Owner List (make smaller) --}}
        <div class="w-full lg:w-1/4 bg-gray-50 flex flex-col p-6 sm:p-8 lg:p-12 min-w-0 min-h-0">
            <div class="mb-5 sm:mb-6 lg:mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2 leading-tight">
                    Dambulla Plan 2001MT65
                </h1>
                <p class="text-lg sm:text-xl text-gray-600">Interactive Plan</p>
            </div>

            {{-- Scroll only the owner list; keep pagination at the bottom --}}
            <div class="flex-1 min-h-0 overflow-y-auto mb-6 sm:mb-8">
                <div
                    data-owner-grid
                    class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 sm:grid-rows-9 sm:h-full"
                ></div>
            </div>

            <div class="mt-auto flex items-center justify-center gap-3 sm:gap-4 pt-2">
                <button
                    type="button"
                    data-page-prev
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-gray-900 flex items-center justify-center hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    aria-label="Previous page"
                >
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>

                <div data-page-buttons class="flex flex-wrap items-center justify-center gap-2 max-w-full"></div>

                <button
                    type="button"
                    data-page-next
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-gray-900 flex items-center justify-center hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    aria-label="Next page"
                >
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Details modal --}}
    <div
        data-details-modal
        class="fixed inset-0 z-50 hidden"
        aria-hidden="true"
    >
        <div data-details-overlay class="absolute inset-0 bg-black/40"></div>

        <div class="relative h-full w-full flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-xl border border-black/10">
                <div class="px-5 py-4 border-b border-black/10 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">Details</div>
                        <div data-details-subtitle class="text-sm text-gray-600"></div>
                    </div>
                    <button
                        type="button"
                        data-details-close
                        class="shrink-0 w-9 h-9 rounded-full border-2 border-gray-900 flex items-center justify-center hover:bg-gray-100 transition-colors"
                        aria-label="Close details"
                    >
                        <span class="text-xl leading-none">&times;</span>
                    </button>
                </div>

                <div class="px-5 py-4">
                    <div data-details-body class="text-sm text-gray-800 space-y-2"></div>
                </div>

                <div class="px-5 py-4 border-t border-black/10 flex justify-end gap-3">
                    <button
                        type="button"
                        data-details-close
                        class="px-4 py-2 rounded-full border-2 border-gray-900 hover:bg-gray-100 transition-colors text-sm font-medium"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
