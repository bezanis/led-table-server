const editor = document.querySelector('[data-display-editor]');

if (editor) {
    const width = 20;
    const height = 10;
    const colors = ['#ff2d95', '#ff8a3d', '#ffe66d', '#69f0ae', '#3de2ff', '#a78bfa', '#ffffff'];
    const pixels = JSON.parse(editor.dataset.state).pixels;
    const board = editor.querySelector('[data-pixel-grid]');
    const textInput = editor.querySelector('#scroll-text-input');
    const counter = editor.querySelector('[data-character-count]');
    const saveStatus = editor.querySelector('[data-save-status]');
    const connectionStatus = editor.querySelector('[data-connection-status]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const cells = [];
    let activeColor = colors[0];
    let activeTool = 'paint';
    let painting = false;
    let hasUnsentPixels = false;
    let textTimer;
    let saveQueue = Promise.resolve();
    let saveRevision = 0;

    const setSaveStatus = (message, type = '') => {
        saveStatus.textContent = message;
        saveStatus.classList.toggle('is-saving', type === 'saving');
        saveStatus.classList.toggle('is-error', type === 'error');
    };

    const setConnectionStatus = (status) => {
        connectionStatus.textContent = status === 'connected' ? 'LIVE' : status === 'disconnected' ? 'OFFLINE' : 'CONNECTING';
        connectionStatus.parentElement.classList.toggle('is-connected', status === 'connected');
        connectionStatus.parentElement.classList.toggle('is-disconnected', status === 'disconnected');
    };

    const renderCell = (x, y) => {
        const cell = cells[y][x];
        const color = pixels[y][x];

        cell.classList.toggle('is-lit', Boolean(color));
        cell.style.setProperty('--pixel-color', color ?? 'transparent');
        cell.setAttribute('aria-label', `Column ${x + 1}, row ${y + 1}${color ? `, ${color}` : ', off'}`);
    };

    for (let y = 0; y < height; y += 1) {
        const row = [];
        cells.push(row);

        for (let x = 0; x < width; x += 1) {
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'pixel-cell';
            cell.setAttribute('role', 'gridcell');
            cell.setAttribute('aria-label', `Column ${x + 1}, row ${y + 1}, off`);
            cell.dataset.x = String(x);
            cell.dataset.y = String(y);
            board.append(cell);
            row.push(cell);
            renderCell(x, y);
        }

    }

    const save = (payload) => {
        const revision = ++saveRevision;
        setSaveStatus('SAVING...', 'saving');

        const send = async () => {
            const response = await fetch('/display-state', {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message ?? 'Could not save the display.');
            }

            if (revision === saveRevision) {
                setSaveStatus('SAVED · LIVE');
            }
        };

        saveQueue = saveQueue.then(send, send);
        saveQueue.catch(() => {
            if (revision === saveRevision) {
                setSaveStatus('SAVE FAILED · TRY AGAIN', 'error');
            }
        });
    };

    const setPixel = (x, y, color) => {
        if (pixels[y][x] === color) {
            return false;
        }

        pixels[y][x] = color;
        renderCell(x, y);
        return true;
    };

    const fill = (startX, startY) => {
        const target = pixels[startY][startX];

        if (target === activeColor) {
            return false;
        }

        const queue = [[startX, startY]];
        let changed = false;

        while (queue.length > 0) {
            const [x, y] = queue.pop();

            if (x < 0 || x >= width || y < 0 || y >= height || pixels[y][x] !== target) {
                continue;
            }

            pixels[y][x] = activeColor;
            renderCell(x, y);
            changed = true;
            queue.push([x + 1, y], [x - 1, y], [x, y + 1], [x, y - 1]);
        }

        return changed;
    };

    const paintCell = (cell) => {
        const x = Number(cell.dataset.x);
        const y = Number(cell.dataset.y);

        if (activeTool === 'fill') {
            hasUnsentPixels = fill(x, y) || hasUnsentPixels;
            return;
        }

        hasUnsentPixels = setPixel(x, y, activeTool === 'erase' ? null : activeColor) || hasUnsentPixels;
    };

    board.addEventListener('pointerdown', (event) => {
        const cell = event.target.closest('.pixel-cell');

        if (!cell) {
            return;
        }

        event.preventDefault();

        if (activeTool === 'fill') {
            paintCell(cell);
            save({ pixels });
            hasUnsentPixels = false;
            return;
        }

        painting = true;
        paintCell(cell);
    });

    board.addEventListener('pointermove', (event) => {
        if (!painting || activeTool === 'fill') {
            return;
        }

        const cell = event.target.closest('.pixel-cell');

        if (cell) {
            paintCell(cell);
        }
    });

    window.addEventListener('pointerup', () => {
        if (!painting) {
            return;
        }

        painting = false;

        if (hasUnsentPixels) {
            save({ pixels });
            hasUnsentPixels = false;
        }
    });

    editor.querySelectorAll('[data-tool]').forEach((button) => {
        button.addEventListener('click', () => {
            activeTool = button.dataset.tool;

            editor.querySelectorAll('[data-tool]').forEach((toolButton) => {
                const selected = toolButton === button;
                toolButton.classList.toggle('is-active', selected);
                toolButton.setAttribute('aria-pressed', String(selected));
            });
        });
    });

    editor.querySelectorAll('[data-color]').forEach((button) => {
        button.addEventListener('click', () => {
            activeColor = button.dataset.color;

            editor.querySelectorAll('[data-color]').forEach((swatch) => {
                const selected = swatch === button;
                swatch.classList.toggle('is-selected', selected);
                swatch.setAttribute('aria-pressed', String(selected));
            });
        });
    });

    editor.querySelector('[data-clear-grid]').addEventListener('click', () => {
        let changed = false;

        for (let y = 0; y < height; y += 1) {
            for (let x = 0; x < width; x += 1) {
                changed = setPixel(x, y, null) || changed;
            }
        }

        if (changed) {
            save({ pixels });
        }
    });

    textInput.addEventListener('input', () => {
        counter.textContent = String(textInput.value.length);
        window.clearTimeout(textTimer);
        setSaveStatus('EDITING...', 'saving');
        textTimer = window.setTimeout(() => save({ scroll_text: textInput.value }), 300);
    });

    const echoConnection = window.Echo?.connector?.pusher?.connection;

    if (echoConnection) {
        echoConnection.bind('state_change', ({ current }) => setConnectionStatus(current));
        setConnectionStatus(echoConnection.state);
    } else {
        setConnectionStatus('disconnected');
    }
}
