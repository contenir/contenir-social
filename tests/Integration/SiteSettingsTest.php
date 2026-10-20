<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Integration;

use Contenir\Social\Profiles;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLink;
use Contenir\Social\Share\ShareLinks;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;

/**
 * A site's whole settings, as the CMS writes them to settings.global.php,
 * read for its profiles and share buttons.
 */
#[Group('integration')]
final class SiteSettingsTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private static function settings(): array
    {
        return [
            'site'       => ['name' => 'Houses Awards'],
            'social'     => [
                'social_facebook'  => 'https://www.facebook.com/housesmagazine',
                'social_twitter'   => null,
                'social_instagram' => 'housesmagazine',
                'social_linkedin'  => null,
                'social_pinterest' => null,
                'social_bluesky'   => 'housesawards.com.au',
            ],
            'extensions' => [
                'social' => [
                    'share_networks' => [
                        ['fields' => ['network' => 'linkedin']],
                        ['fields' => ['network' => 'email']],
                        ['fields' => ['network' => 'pinterest']],
                        ['fields' => ['network' => 'copy']],
                    ],
                    'share_labels'   => '0',
                    'share_utm'      => '1',
                ],
            ],
        ];
    }

    #[Test]
    public function buildsThePagesShareButtonsInTheSitesOrder(): void
    {
        $links = (new ShareLinks(ShareConfig::fromSettings(self::settings())))->for(
            new Page('https://www.housesawards.com.au/winners', 'The 2026 winners'),
        );

        static::assertSame(
            [
                'https://www.linkedin.com/sharing/share-offsite/?url=https%3A%2F%2Fwww.housesawards.com.au%2Fwinners'
                    . '%3Futm_source%3Dlinkedin%26utm_medium%3Dsocial%26utm_campaign%3Dshare',
                'mailto:?subject=The%202026%20winners&body=https%3A%2F%2Fwww.housesawards.com.au%2Fwinners'
                    . '%3Futm_source%3Demail%26utm_medium%3Demail%26utm_campaign%3Dshare',
                'https://www.housesawards.com.au/winners?utm_source=copy&utm_medium=social&utm_campaign=share',
            ],
            array_map(static fn(ShareLink $link): string => $link->href, $links),
        );
    }

    #[Test]
    public function readsAnExistingSitesProfilesWithTheNewNetworks(): void
    {
        static::assertSame(
            [
                'https://www.facebook.com/housesmagazine',
                'https://www.instagram.com/housesmagazine/',
                'https://bsky.app/profile/housesawards.com.au',
            ],
            Profiles::fromSettings(self::settings())->sameAs(),
        );
    }
}
