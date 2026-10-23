# LED Table Server

Standalone Laravel application for a future LED Table web experience. It uses Laravel broadcasting and Laravel Reverb for real-time events, and is intended to deploy on Laravel Cloud.

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

## Laravel Cloud

1. Push this project to its own Git repository and create an application in Laravel Cloud from that repository.
2. Create a managed WebSocket cluster under **Resources > WebSockets**.
3. Attach the cluster's WebSocket application to this environment, then redeploy.

Laravel Cloud injects the `REVERB_*` and `VITE_REVERB_*` values for an attached WebSocket application. The Laravel Reverb driver and Echo client in this project are already configured to use those values. Keep `REVERB_APP_SECRET` server-side; do not expose it to browser code.

See the [Laravel Cloud WebSockets documentation](https://laravel.com/cloud/docs/resources/websockets) and [Laravel Reverb documentation](https://laravel.com/docs/13.x/reverb) for cluster configuration and broadcasting details.
