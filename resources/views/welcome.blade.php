<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>LED Table — Pixel Studio</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="studio-page">
        <div class="studio-shell" data-display-editor data-state="{{ json_encode($state) }}">
            <header class="topbar">
                <a class="brand" href="{{ route('display.editor') }}" aria-label="LED Table home">
                    <span class="brand-mark" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
                    <span>LED<span class="brand-light">TABLE</span></span>
                </a>
                <div class="topbar-right">
                    <span class="connection"><span class="connection-dot"></span><span data-connection-status>CONNECTING</span></span>
                    <a class="client-link" href="{{ route('display.client') }}" target="_blank" rel="noreferrer">OPEN CLIENT VIEW <span aria-hidden="true">↗</span></a>
                </div>
            </header>

            <main>
                <section class="intro">
                    <div>
                        <p class="eyebrow"><span class="eyebrow-line"></span> YOUR PIXEL PLAYGROUND</p>
                        <h1>LED <span>TABLE.</span></h1>
                        <p class="intro-copy">Make some noise. Paint the pixels. Watch it light up live.</p>
                    </div>
                    <div class="intro-sticker" aria-hidden="true"><span>100%</span><small>RAD</small></div>
                </section>

                <section class="studio-card" aria-label="LED Table editor">
                    <div class="card-heading">
                        <div>
                            <span class="section-index">01 / MESSAGE</span>
                            <label for="scroll-text-input">SCROLL TEXT</label>
                        </div>
                        <span class="save-status" data-save-status>ALL SYSTEMS GO</span>
                    </div>
                    <div class="text-field-wrap">
                        <input id="scroll-text-input" class="scroll-input" type="text" maxlength="120" autocomplete="off" value="{{ $state['scroll_text'] }}" placeholder="TYPE SOMETHING RAD...">
                        <span class="input-counter"><span data-character-count>{{ mb_strlen($state['scroll_text']) }}</span><span>/ 120</span></span>
                    </div>

                    <div class="canvas-heading">
                        <div>
                            <span class="section-index">02 / PIXEL ART</span>
                            <h2>THE LIGHT BOARD <span>✦</span></h2>
                        </div>
                        <span class="grid-size">20 <i>×</i> 10 <small>PIXELS</small></span>
                    </div>

                    <div class="canvas-layout">
                        <aside class="tool-rail" aria-label="Paint tools">
                            <span class="rail-label">TOOLS</span>
                            <button class="tool-button is-active" type="button" data-tool="paint" aria-pressed="true" title="Paint">
                                <span aria-hidden="true">✎</span><small>PAINT</small>
                            </button>
                            <button class="tool-button" type="button" data-tool="erase" aria-pressed="false" title="Erase">
                                <span aria-hidden="true">⌫</span><small>ERASE</small>
                            </button>
                            <button class="tool-button" type="button" data-tool="fill" aria-pressed="false" title="Fill connected pixels">
                                <span aria-hidden="true">▧</span><small>FILL</small>
                            </button>
                            <span class="rail-divider"></span>
                            <button class="tool-button clear-tool" type="button" data-clear-grid title="Clear the board">
                                <span aria-hidden="true">↺</span><small>CLEAR</small>
                            </button>
                        </aside>

                        <div class="board-wrap">
                            <div class="board-topline"><span>LED MATRIX / LIVE PREVIEW</span><span><i></i> 200 PIXELS</span></div>
                            <div class="pixel-board" data-pixel-grid role="grid" aria-label="20 columns by 10 rows pixel art board"></div>
                            <div class="board-bottomline"><span>CLICK + DRAG TO PAINT</span><span>LET YOUR CREATIVITY GLOW <b>✦</b></span></div>
                        </div>
                    </div>

                    <div class="palette-row">
                        <div class="palette-label"><span class="section-index">03 / COLOR</span><strong>CHOOSE YOUR GLOW</strong></div>
                        <div class="swatches" role="group" aria-label="Color palette">
                            @foreach (\App\Models\LedDisplay::COLORS as $color)
                                <button class="swatch{{ $loop->first ? ' is-selected' : '' }}" type="button" style="--swatch: {{ $color }}" data-color="{{ $color }}" aria-label="Select {{ $color }} paint" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"></button>
                            @endforeach
                        </div>
                        <span class="palette-hint">PICK A COLOR <span>→</span></span>
                    </div>
                </section>

                <footer class="studio-footer">
                    <span>MADE OF PIXELS <b>✦</b> POWERED BY GOOD VIBRATIONS</span>
                    <span>EST. RIGHT NOW <b>’86</b></span>
                </footer>
            </main>
        </div>
    </body>
</html>
