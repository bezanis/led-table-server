<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LED Table — Live Display</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="client-page">
        <main class="client-shell" data-display-client data-state="{{ json_encode($state) }}">
            <header class="client-topbar">
                <a class="brand" href="{{ route('display.editor') }}">
                    <span class="brand-mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                    <span>LED<span class="brand-light">TABLE</span></span>
                </a>
                <span class="connection"><span class="connection-dot"></span><span data-connection-status>CONNECTING</span></span>
            </header>
            <section class="client-stage">
                <p class="eyebrow"><span class="eyebrow-line"></span> LIVE FROM THE LIGHT BOARD <span class="eyebrow-line"></span></p>
                <div class="marquee-frame">
                    <div class="marquee-track" data-marquee-track><span data-client-text></span><span data-marquee-copy aria-hidden="true"></span></div>
                </div>
                <div class="client-board-frame">
                    <div class="client-board-label"><span>20 × 10 PIXEL MATRIX</span><span>LIVE <i></i></span></div>
                    <div class="client-pixel-board" data-pixel-grid role="img" aria-label="Live LED pixel display"></div>
                </div>
                <p class="client-caption"><span>✦</span> GOOD VIBRATIONS, ONE PIXEL AT A TIME <span>✦</span></p>
            </section>
        </main>
    </body>
</html>
