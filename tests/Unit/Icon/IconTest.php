<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Icon;

use Contenir\Social\Icon\Icon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_fill;
use function array_map;
use function count;
use function preg_match;

#[Group('unit')]
final class IconTest extends TestCase
{
    /**
     * @return array<string, array{Icon, int}>
     */
    public static function iconProvider(): array
    {
        return [
            'facebook'  => [Icon::Facebook, 1],
            'instagram' => [Icon::Instagram, 1],
            'linkedin'  => [Icon::LinkedIn, 1],
            'x'         => [Icon::X, 1],
            'youtube'   => [Icon::YouTube, 1],
            'tiktok'    => [Icon::TikTok, 1],
            'pinterest' => [Icon::Pinterest, 1],
            'threads'   => [Icon::Threads, 1],
            'bluesky'   => [Icon::Bluesky, 1],
            'mastodon'  => [Icon::Mastodon, 1],
            'whatsapp'  => [Icon::WhatsApp, 1],
            'telegram'  => [Icon::Telegram, 1],
            'reddit'    => [Icon::Reddit, 1],
            'email'     => [Icon::Email, 2],
            'link'      => [Icon::Link, 3],
            'share'     => [Icon::Share, 1],
        ];
    }

    #[Test]
    #[DataProvider('iconProvider')]
    public function drawsEachIconWithPathData(Icon $icon, int $paths): void
    {
        $data = $icon->paths();

        static::assertSame(
            [$paths, array_fill(0, $paths, value: 1)],
            [count($data), array_map(static fn(string $path): int => preg_match('/^M[\d.\-]/', $path), $data)],
        );
    }
}
