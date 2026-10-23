<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedDisplay extends Model
{
    public const WIDTH = 20;

    public const HEIGHT = 10;

    public const COLORS = [
        '#ff2d95',
        '#ff8a3d',
        '#ffe66d',
        '#69f0ae',
        '#3de2ff',
        '#a78bfa',
        '#ffffff',
    ];

    protected $fillable = [
        'id',
        'scroll_text',
        'pixels',
    ];

    protected function casts(): array
    {
        return [
            'pixels' => 'array',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'scroll_text' => 'WELCOME TO THE LED TABLE',
                'pixels' => array_fill(0, self::HEIGHT, array_fill(0, self::WIDTH, null)),
            ],
        );
    }
}
