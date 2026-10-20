<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit;

use Contenir\Social\Network;
use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;

#[Group('unit')]
final class ProfilesTest extends TestCase
{
    /**
     * Settings as a site saved them with the CMS's Social Media Links plugin.
     *
     * @return array<string, mixed>
     */
    private static function legacySettings(): array
    {
        return [
            'social' => [
                'social_facebook'  => 'https://www.facebook.com/housesmagazine',
                'social_twitter'   => '@HousesMag',
                'social_instagram' => 'housesmagazine',
                'social_linkedin'  => null,
                'social_pinterest' => '',
            ],
        ];
    }

    #[Test]
    public function derivesTwitterSiteFromTheXProfile(): void
    {
        static::assertSame('@HousesMag', Profiles::fromSettings(self::legacySettings())->twitterSite());
    }

    #[Test]
    public function findsNoProfileOnANetworkTheSiteIsNotOn(): void
    {
        static::assertNull(Profiles::fromSettings(self::legacySettings())->get(Network::LinkedIn));
    }

    #[Test]
    public function findsTheProfileOnANetwork(): void
    {
        static::assertSame(
            'https://www.instagram.com/housesmagazine/',
            Profiles::fromSettings(self::legacySettings())->get(Network::Instagram)?->url,
        );
    }

    #[Test]
    public function hasNoProfilesWithoutSettings(): void
    {
        $profiles = Profiles::fromSettings([]);

        static::assertSame([[], [], null], [$profiles->all(), $profiles->problems, $profiles->twitterSite()]);
    }

    #[Test]
    public function keepsTheLastProfileGivenForANetwork(): void
    {
        $profiles = new Profiles([
            ProfileUrl::parse(Network::X, '@first'),
            ProfileUrl::parse(Network::X, '@second'),
        ]);

        static::assertSame(['https://x.com/second'], $profiles->sameAs());
    }

    #[Test]
    public function leavesOutAValueThatIsNotAProfileAndSaysWhy(): void
    {
        $profiles = Profiles::fromSettings(['social' => [
            'social_facebook' => 'https://www.instagram.com/houses/',
            'social_linkedin' => 'https://www.linkedin.com/company/houses/',
        ]]);

        static::assertSame(
            [
                ['https://www.linkedin.com/company/houses/'],
                [
                    'social.social_facebook' =>
                        'That address is on www.instagram.com, not Facebook. Enter the'
                            . ' profile\'s address, such as https://www.facebook.com/yourpage.',
                ],
            ],
            [$profiles->sameAs(), $profiles->problems],
        );
    }

    #[Test]
    public function listsProfilesInTheOrderSitesListThemWhateverOrderTheyArrive(): void
    {
        $profiles = new Profiles([
            ProfileUrl::parse(Network::Mastodon, '@houses@aus.social'),
            ProfileUrl::parse(Network::Facebook, '@houses'),
        ]);

        static::assertSame(
            ['https://www.facebook.com/houses', 'https://aus.social/@houses'],
            $profiles->sameAs(),
        );
    }

    #[Test]
    public function readsProfilesFromTheKeysTheCmsUsed(): void
    {
        static::assertSame(
            [
                'https://www.facebook.com/housesmagazine',
                'https://www.instagram.com/housesmagazine/',
                'https://x.com/HousesMag',
            ],
            Profiles::fromSettings(self::legacySettings())->sameAs(),
        );
    }

    #[Test]
    public function readsTheNewNetworksBesideThem(): void
    {
        $profiles = Profiles::fromSettings(['social' => [
            'social_youtube'  => '@houses',
            'social_tiktok'   => '@houses',
            'social_threads'  => '@houses',
            'social_bluesky'  => 'houses.bsky.social',
            'social_mastodon' => '@houses@aus.social',
        ]]);

        static::assertSame(
            [Network::YouTube, Network::TikTok, Network::Threads, Network::Bluesky, Network::Mastodon],
            array_map(static fn(ProfileUrl $profile): Network => $profile->network, $profiles->all()),
        );
    }
}
