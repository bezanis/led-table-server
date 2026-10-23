# LED Table Server

Laravel application for a shared LED Table pixel display. Use the editor to set scrolling text and paint the 20×10 LED matrix; the client view updates live over Laravel Reverb.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm

## Local development

Install the application and frontend dependencies:

```bash
composer setup
```

Run the Laravel app and frontend dev server:

```bash
composer run dev
```

In a second terminal, start the local WebSocket server:

```bash
php artisan reverb:start
```

The local Reverb credentials and Echo client settings are provided in `.env.example`. Update them in `.env` if needed.

Open [http://localhost:8000](http://localhost:8000) for the editor. Open [http://localhost:8000/client](http://localhost:8000/client) on a display device to show the live message and pixel art. Display state is stored in the configured database, so both views share the same saved text and pixels.

## Laravel Cloud

The production application is available at [led-table-server-production-ed5hwe.laravel.cloud](https://led-table-server-production-ed5hwe.laravel.cloud), with the editor at `/` and the live display at `/client`. It uses a managed Laravel MySQL database and a managed Reverb WebSocket cluster in `us-east-2`. Pushes to `main` deploy automatically and run database migrations.

Laravel Cloud injects the `REVERB_*` and `VITE_REVERB_*` values for an attached WebSocket application. The Laravel Reverb driver and Echo client in this project are already configured to use those values. Keep `REVERB_APP_SECRET` server-side; do not expose it to browser code.

See the [Laravel Cloud WebSockets documentation](https://laravel.com/cloud/docs/resources/websockets) and [Laravel Reverb documentation](https://laravel.com/docs/13.x/reverb) for cluster configuration and broadcasting details.
