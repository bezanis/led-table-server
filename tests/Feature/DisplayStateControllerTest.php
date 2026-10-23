<?php

namespace Tests\Feature;

use App\Events\DisplayUpdated;
use App\Models\LedDisplay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DisplayStateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_and_client_pages_render_the_shared_display(): void
    {
        $editor = $this->get('/');

        $editor->assertOk();
        $editor->assertSee('SCROLL TEXT');
        $editor->assertSee('THE LIGHT BOARD');
        $editor->assertSee('data-display-editor', false);

        $client = $this->get('/client');

        $client->assertOk();
        $client->assertSee('LIVE FROM THE LIGHT BOARD');
        $client->assertSee('data-display-client', false);
        $this->assertDatabaseHas('led_displays', [
            'id' => 1,
            'scroll_text' => 'WELCOME TO THE LED TABLE',
        ]);
    }

    public function test_valid_display_updates_are_persisted_and_broadcast(): void
    {
        Event::fake([DisplayUpdated::class]);

        $pixels = array_fill(0, 10, array_fill(0, 20, null));
        $pixels[0][0] = '#ff2d95';

        $response = $this->putJson('/display-state', [
            'scroll_text' => 'HELLO FROM THE TABLE',
            'pixels' => $pixels,
        ]);

        $response->assertOk();
        $response->assertExactJson([
            'scroll_text' => 'HELLO FROM THE TABLE',
            'pixels' => $pixels,
        ]);
        $this->assertDatabaseHas('led_displays', [
            'id' => 1,
            'scroll_text' => 'HELLO FROM THE TABLE',
        ]);
        $this->assertSame($pixels, LedDisplay::query()->findOrFail(1)->pixels);
        Event::assertDispatched(
            DisplayUpdated::class,
            fn (DisplayUpdated $event): bool => $event->state === [
                'scroll_text' => 'HELLO FROM THE TABLE',
                'pixels' => $pixels,
            ],
        );
    }

    public function test_websocket_config_exposes_only_public_reverb_settings(): void
    {
        config([
            'broadcasting.connections.reverb.key' => 'public-app-key',
            'broadcasting.connections.reverb.options.host' => 'ws-example-reverb.laravel.cloud',
        ]);

        $this->getJson('/display-config')
            ->assertOk()
            ->assertExactJson([
                'host' => 'ws-example-reverb.laravel.cloud',
                'key' => 'public-app-key',
            ]);
    }

    public function test_websocket_config_is_unavailable_without_an_attached_cluster(): void
    {
        config([
            'broadcasting.connections.reverb.key' => null,
            'broadcasting.connections.reverb.options.host' => null,
        ]);

        $this->getJson('/display-config')->assertStatus(503);

        config([
            'broadcasting.connections.reverb.key' => 'local',
            'broadcasting.connections.reverb.options.host' => '127.0.0.1',
        ]);

        $this->getJson('/display-config')->assertStatus(503);
    }

    public function test_invalid_pixel_colors_are_rejected_without_changing_the_display(): void
    {
        Event::fake([DisplayUpdated::class]);

        $display = LedDisplay::current();
        $pixels = array_fill(0, 10, array_fill(0, 20, null));
        $pixels[0][0] = '#000000';

        $response = $this->putJson('/display-state', ['pixels' => $pixels]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('pixels.0.0');
        $this->assertSame('WELCOME TO THE LED TABLE', $display->fresh()->scroll_text);
        Event::assertNotDispatched(DisplayUpdated::class);
    }

    public function test_grid_updates_with_the_wrong_dimensions_are_rejected(): void
    {
        $response = $this->putJson('/display-state', [
            'pixels' => array_fill(0, 9, array_fill(0, 20, null)),
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('pixels');
        $this->assertDatabaseMissing('led_displays', ['id' => 1]);
    }

    public function test_scroll_text_is_escaped_in_the_editor_and_client_pages(): void
    {
        $this->putJson('/display-state', [
            'scroll_text' => '<script>alert("hi")</script>',
        ])->assertOk();

        $editor = $this->get('/');
        $client = $this->get('/client');

        $editor->assertDontSee('<script>alert("hi")</script>', false);
        $client->assertDontSee('<script>alert("hi")</script>', false);
        $editor->assertSee('&lt;script&gt;', false);
        $client->assertSee('&lt;script&gt;', false);
    }
}
