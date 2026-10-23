const client = document.querySelector('[data-display-client]');

if (client) {
    const width = 20;
    const height = 10;
    const board = client.querySelector('[data-pixel-grid]');
    const text = client.querySelector('[data-client-text]');
    const textCopy = client.querySelector('[data-marquee-copy]');
    const connectionStatus = client.querySelector('[data-connection-status]');
    const cells = [];

    for (let index = 0; index < width * height; index += 1) {
        const pixel = document.createElement('span');
        pixel.className = 'client-pixel';
        pixel.setAttribute('aria-hidden', 'true');
        board.append(pixel);
        cells.push(pixel);
    }

    const render = (state) => {
        const message = state.scroll_text || 'LED TABLE';
        text.textContent = message;
        textCopy.textContent = message;

        state.pixels.forEach((row, y) => {
            row.forEach((color, x) => {
                const pixel = cells[y * width + x];
                pixel.classList.toggle('is-lit', Boolean(color));
                pixel.style.setProperty('--pixel-color', color ?? 'transparent');
            });
        });
    };

    const setConnectionStatus = (status) => {
        connectionStatus.textContent = status === 'connected' ? 'LIVE' : status === 'disconnected' ? 'OFFLINE' : 'CONNECTING';
        connectionStatus.parentElement.classList.toggle('is-connected', status === 'connected');
        connectionStatus.parentElement.classList.toggle('is-disconnected', status === 'disconnected');
    };

    render(JSON.parse(client.dataset.state));

    const echoConnection = window.Echo?.connector?.pusher?.connection;

    if (echoConnection) {
        echoConnection.bind('state_change', ({ current }) => setConnectionStatus(current));
        setConnectionStatus(echoConnection.state);
        window.Echo.channel('display').listen('.display.updated', (event) => render(event.state));
    } else {
        setConnectionStatus('disconnected');
    }
}
