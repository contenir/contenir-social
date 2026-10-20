<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Share;

use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;

#[Group('unit')]
final class ShareTargetTest extends TestCase
{
    /**
     * @return array<string, array{ShareTarget, array{string, bool, bool, string, string, string}}>
     */
    public static function targetProvider(): array
    {
        return [
            'facebook'  => [
                ShareTarget::Facebook,
                ['Facebook', false, false, 'facebook', 'Share on Facebook', 'Share on Facebook (opens in a new tab)'],
            ],
            'x'         => [ShareTarget::X, ['X', false, false, 'x', 'Share on X', 'Share on X (opens in a new tab)']],
            'linkedin'  => [
                ShareTarget::LinkedIn,
                ['LinkedIn', false, false, 'linkedin', 'Share on LinkedIn', 'Share on LinkedIn (opens in a new tab)'],
            ],
            'whatsapp'  => [
                ShareTarget::WhatsApp,
                ['WhatsApp', false, false, 'whatsapp', 'Share on WhatsApp', 'Share on WhatsApp (opens in a new tab)'],
            ],
            'email'     => [ShareTarget::Email, ['Email', false, false, 'email', 'Share by email', 'Share by email']],
            'bluesky'   => [
                ShareTarget::Bluesky,
                ['Bluesky', false, false, 'bluesky', 'Share on Bluesky', 'Share on Bluesky (opens in a new tab)'],
            ],
            'threads'   => [
                ShareTarget::Threads,
                ['Threads', false, false, 'threads', 'Share on Threads', 'Share on Threads (opens in a new tab)'],
            ],
            'reddit'    => [
                ShareTarget::Reddit,
                ['Reddit', false, false, 'reddit', 'Share on Reddit', 'Share on Reddit (opens in a new tab)'],
            ],
            'pinterest' => [
                ShareTarget::Pinterest,
                [
                    'Pinterest',
                    false,
                    true,
                    'pinterest',
                    'Share on Pinterest',
                    'Share on Pinterest (opens in a new tab)',
                ],
            ],
            'telegram'  => [
                ShareTarget::Telegram,
                ['Telegram', false, false, 'telegram', 'Share on Telegram', 'Share on Telegram (opens in a new tab)'],
            ],
            'copy link' => [ShareTarget::CopyLink, ['Copy link', true, false, 'link', 'Copy link', 'Copy link']],
            'share'     => [ShareTarget::Share, ['Share…', true, false, 'share', 'Share…', 'Share…']],
        ];
    }

    /**
     * @param array{string, bool, bool, string, string, string} $expected Label, button, needs an image, icon, names
     */
    #[Test]
    #[DataProvider('targetProvider')]
    public function describesItself(ShareTarget $target, array $expected): void
    {
        static::assertSame($expected, [
            $target->label(),
            $target->isButton(),
            $target->needsImage(),
            $target->icon()->value,
            $target->name(),
            $target->newTabName(),
        ]);
    }

    #[Test]
    public function listsTargetsInTheOrderSitesOfferThem(): void
    {
        static::assertSame(
            [
                'facebook',
                'x',
                'linkedin',
                'whatsapp',
                'email',
                'bluesky',
                'threads',
                'reddit',
                'pinterest',
                'telegram',
                'copy',
                'share',
            ],
            array_map(static fn(ShareTarget $target): string => $target->value, ShareTarget::cases()),
        );
    }
}
