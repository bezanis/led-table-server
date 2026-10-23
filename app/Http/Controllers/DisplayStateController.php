<?php

namespace App\Http\Controllers;

use App\Events\DisplayUpdated;
use App\Models\LedDisplay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisplayStateController extends Controller
{
    public function editor(): View
    {
        return view('welcome', ['state' => $this->state()]);
    }

    public function client(): View
    {
        return view('client', ['state' => $this->state()]);
    }

    public function show(): JsonResponse
    {
        return response()->json($this->state());
    }

    public function websocketConfig(): JsonResponse
    {
        $connection = config('broadcasting.connections.reverb');
        $host = $connection['options']['host'] ?? null;
        $key = $connection['key'] ?? null;
        $localHosts = ['127.0.0.1', 'localhost', '0.0.0.0', '::1'];

        abort_if(
            ! is_string($host) || $host === '' || in_array($host, $localHosts, true) ||
                ! is_string($key) || $key === '',
            503,
            'The WebSocket application is not configured.',
        );

        return response()->json([
            'host' => $host,
            'key' => $key,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scroll_text' => ['sometimes', 'required', 'string', 'max:120'],
            'pixels' => ['sometimes', 'required', 'array', 'size:'.LedDisplay::HEIGHT],
            'pixels.*' => ['required_with:pixels', 'array', 'size:'.LedDisplay::WIDTH],
            'pixels.*.*' => ['nullable', 'string', 'in:'.implode(',', LedDisplay::COLORS)],
        ]);

        abort_if($validated === [], 422, 'Provide scroll_text or pixels.');

        $display = LedDisplay::current();
        $display->fill($validated)->save();

        $state = $this->state($display);

        broadcast(new DisplayUpdated($state));

        return response()->json($state);
    }

    private function state(?LedDisplay $display = null): array
    {
        $display ??= LedDisplay::current();

        return [
            'scroll_text' => $display->scroll_text,
            'pixels' => $display->pixels,
        ];
    }
}
