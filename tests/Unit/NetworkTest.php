<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit;

use Contenir\Social\Network;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;

#[Group('unit')]
final class NetworkTest extends TestCase
{
    /**
     * @return array<string, array{Network, string, string, string, ?string}>
     */
    public static function networkProvider(): array
    {
        return [
            'facebook'  => [
                Network::Facebook,
                'Facebook',
                'social.social_facebook',
                'https://www.facebook.com/yourpage',
                '@yourname',
            ],
            'instagram' => [
                Network::Instagram,
                'Instagram',
                'social.social_instagram',
                'https://www.instagram.com/yourname/',
                '@yourname',
            ],
            'linkedin'  => [
                Network::LinkedIn,
                'LinkedIn',
                'social.social_linkedin',
                'https://www.linkedin.com/company/yourname/',
                null,
            ],
            'x'         => [Network::X, 'X', 'social.social_twitter', 'https://x.com/yourname', '@yourname'],
            'youtube'   => [
                Network::YouTube,
                'YouTube',
                'social.social_youtube',
                'https://www.youtube.com/@yourname',
                '@yourname',
            ],
            'tiktok'    => [
                Network::TikTok,
                'TikTok',
                'social.social_tiktok',
                'https://www.tiktok.com/@yourname',
                '@yourname',
            ],
            'pinterest' => [
                Network::Pinterest,
                'Pinterest',
                'social.social_pinterest',
                'https://www.pinterest.com/yourname/',
                '@yourname',
            ],
            'threads'   => [
                Network::Threads,
                'Threads',
                'social.social_threads',
                'https://www.threads.com/@yourname',
                '@yourname',
            ],
            'bluesky'   => [
                Network::Bluesky,
                'Bluesky',
                'social.social_bluesky',
                'https://bsky.app/profile/yourname.bsky.social',
                'yourname.bsky.social',
            ],
            'mastodon'  => [
                Network::Mastodon,
                'Mastodon',
                'social.social_mastodon',
                'https://mastodon.social/@yourname',
                '@yourname@mastodon.social',
            ],
            'vimeo'     => [
                Network::Vimeo,
                'Vimeo',
                'social.social_vimeo',
                'https://vimeo.com/yourname',
                '@yourname',
            ],
            'behance'   => [
                Network::Behance,
                'Behance',
                'social.social_behance',
                'https://www.behance.net/yourname',
                '@yourname',
            ],
        ];
    }

    #[Test]
    #[DataProvider('networkProvider')]
    public function describesItselfToEditorsAndSettings(
        Network $network,
        string $label,
        string $settingKey,
        string $example,
        ?string $handleExample,
    ): void {
        static::assertSame([$label, $settingKey, $example, $handleExample], [
            $network->label(),
            $network->settingKey(),
            $network->example(),
            $network->handleExample(),
        ]);
    }

    #[Test]
    public function drawsItsOwnMark(): void
    {
        static::assertSame(
            [
                'facebook',
                'instagram',
                'linkedin',
                'x',
                'youtube',
                'tiktok',
                'pinterest',
                'threads',
                'bluesky',
                'mastodon',
                'vimeo',
                'behance',
            ],
            array_map(static fn(Network $network): string => $network->icon()->value, Network::cases()),
        );
    }

    #[Test]
    public function listsNetworksInTheOrderSitesListThem(): void
    {
        static::assertSame(
            [
                'facebook',
                'instagram',
                'linkedin',
                'x',
                'youtube',
                'tiktok',
                'pinterest',
                'threads',
                'bluesky',
                'mastodon',
                'vimeo',
                'behance',
            ],
            array_map(static fn(Network $network): string => $network->value, Network::cases()),
        );
    }
}
