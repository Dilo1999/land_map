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

            {{-- Zoom controls --}}
            <div class="absolute left-4 top-4 sm:left-6 sm:top-6 lg:left-10 lg:top-10 z-10 flex flex-col gap-2">
                <button type="button" data-zoom-in class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-gray-900 bg-white/90 backdrop-blur flex items-center justify-center hover:bg-gray-100 transition-colors" aria-label="Zoom in">
                    <span class="text-xl leading-none">+</span>
                </button>
                <button type="button" data-zoom-out class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-gray-900 bg-white/90 backdrop-blur flex items-center justify-center hover:bg-gray-100 transition-colors" aria-label="Zoom out">
                    <span class="text-xl leading-none">−</span>
                </button>
                <button type="button" data-zoom-reset class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-gray-900 bg-white/90 backdrop-blur flex items-center justify-center hover:bg-gray-100 transition-colors" aria-label="Reset zoom">
                    <span class="text-sm font-semibold">1:1</span>
                </button>

                <button
                    type="button"
                    data-view-details
                    class="mt-2 px-4 py-2 rounded-full border-2 border-gray-900 bg-white/90 backdrop-blur hover:bg-gray-100 transition-colors text-sm font-medium"
                >
                    View details
                </button>
            </div>
        </div>

        {{-- Right Panel - Owner List (make smaller) --}}
        <div class="w-full lg:w-1/4 bg-gray-50 flex flex-col p-6 sm:p-8 lg:p-12 min-w-0 overflow-y-auto">
            <div class="mb-5 sm:mb-6 lg:mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2 leading-tight">
                    Dambulla Plan 2001MT65
                </h1>
                <p class="text-lg sm:text-xl text-gray-600">Interactive Plan</p>
            </div>

            <div class="flex-1 mb-6 sm:mb-8 min-h-0">
                <div
                    data-owner-grid
                    class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 sm:grid-rows-9 sm:h-full"
                ></div>
            </div>

            <div class="flex items-center justify-center gap-4">
                <button
                    type="button"
                    data-page-prev
                    class="w-10 h-10 rounded-full border-2 border-gray-900 flex items-center justify-center hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    aria-label="Previous page"
                >
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>

                <div class="flex gap-2">
                    @foreach ([1, 2, 3] as $page)
                        <button
                            type="button"
                            data-page-num="{{ $page }}"
                            class="w-10 h-10 rounded-full font-medium transition-colors text-gray-900 hover:bg-gray-100"
                        >{{ $page }}</button>
                    @endforeach
                </div>

                <button
                    type="button"
                    data-page-next
                    class="w-10 h-10 rounded-full border-2 border-gray-900 flex items-center justify-center hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                    aria-label="Next page"
                >
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
