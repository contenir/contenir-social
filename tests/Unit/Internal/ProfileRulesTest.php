<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Internal;

use Contenir\Social\Internal\ProfileRules;
use Contenir\Social\Network;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ProfileRulesTest extends TestCase
{
    /**
     * @return array<string, array{Network, string, string}>
     */
    public static function accountProvider(): array
    {
        return [
            'facebook'          => [Network::Facebook, '/houses', 'houses'],
            'facebook nested'   => [Network::Facebook, '/pages/Houses/1/', 'pages/Houses/1'],
            'facebook sharing'  => [Network::Facebook, '/shared', 'shared'],
            'instagram'         => [Network::Instagram, '/houses/', 'houses'],
            'instagram p-word'  => [Network::Instagram, '/plans', 'plans'],
            'linkedin'          => [Network::LinkedIn, '/company/houses', 'company/houses'],
            'x'                 => [Network::X, '/houses', 'houses'],
            'x i-word'          => [Network::X, '/insider', 'insider'],
            'youtube'           => [Network::YouTube, '/@houses', '@houses'],
            'tiktok'            => [Network::TikTok, '/@houses', 'houses'],
            'pinterest'         => [Network::Pinterest, '/houses/', 'houses'],
            'pinterest p-word'  => [Network::Pinterest, '/pinboard', 'pinboard'],
            'threads'           => [Network::Threads, '/@houses', 'houses'],
            'bluesky lowercase' => [Network::Bluesky, '/profile/Houses.BSKY.social', 'houses.bsky.social'],
            'mastodon'          => [Network::Mastodon, '/@houses', 'houses'],
        ];
    }

    /**
     * @return array<string, array{Network, string}>
     */
    public static function notAnAccountProvider(): array
    {
        return [
            'facebook root'           => [Network::Facebook, '/'],
            'facebook sharer'         => [Network::Facebook, '/sharer.php'],
            'facebook dialog'         => [Network::Facebook, '/dialog/feed'],
            'facebook profile'        => [Network::Facebook, '/profile.php'],
            'facebook prefixed'       => [Network::Facebook, 'x/houses'],
            'facebook with a newline' => [Network::Facebook, "/houses\n"],
            'instagram post'          => [Network::Instagram, '/p/'],
            'instagram reels'         => [Network::Instagram, '/reels'],
            'instagram nested'        => [Network::Instagram, '/houses/tagged'],
            'instagram prefixed'      => [Network::Instagram, '/x/@/houses'],
            'instagram too long'      => [Network::Instagram, '/abcdefghijklmnopqrstuvwxyz12345'],
            'instagram with newline'  => [Network::Instagram, "/houses\n"],
            'linkedin feed'           => [Network::LinkedIn, '/feed/'],
            'linkedin prefixed'       => [Network::LinkedIn, '/x/company/houses'],
            'linkedin nested'         => [Network::LinkedIn, '/company/houses/posts'],
            'linkedin with a newline' => [Network::LinkedIn, "/company/houses\n"],
            'x home'                  => [Network::X, '/home'],
            'x home in capitals'      => [Network::X, '/Home'],
            'x intent'                => [Network::X, '/intent/'],
            'x prefixed'              => [Network::X, '/a/houses'],
            'x nested'                => [Network::X, '/houses/status/1'],
            'x too long'              => [Network::X, '/abcdefghijklmnop'],
            'x with a newline'        => [Network::X, "/houses\n"],
            'youtube video'           => [Network::YouTube, '/watch'],
            'youtube prefixed'        => [Network::YouTube, '/x/@houses'],
            'youtube nested'          => [Network::YouTube, '/@houses/videos'],
            'youtube with a newline'  => [Network::YouTube, "/@houses\n"],
            'tiktok without an @'     => [Network::TikTok, '/houses'],
            'tiktok prefixed'         => [Network::TikTok, '/x/@houses'],
            'tiktok nested'           => [Network::TikTok, '/@houses/video/1'],
            'tiktok with a newline'   => [Network::TikTok, "/@houses\n"],
            'pinterest pin'           => [Network::Pinterest, '/pin/'],
            'pinterest prefixed'      => [Network::Pinterest, '/x/houses'],
            'pinterest nested'        => [Network::Pinterest, '/houses/board'],
            'pinterest with newline'  => [Network::Pinterest, "/houses\n"],
            'threads without an @'    => [Network::Threads, '/houses'],
            'threads prefixed'        => [Network::Threads, '/x/@houses'],
            'threads nested'          => [Network::Threads, '/@houses/post/1'],
            'threads with a newline'  => [Network::Threads, "/@houses\n"],
            'bluesky feed'            => [Network::Bluesky, '/profile/houses'],
            'bluesky prefixed'        => [Network::Bluesky, '/x/profile/houses.bsky.social'],
            'bluesky nested'          => [Network::Bluesky, '/profile/houses.bsky.social/post/1'],
            'bluesky with a newline'  => [Network::Bluesky, "/profile/houses.bsky.social\n"],
            'mastodon without an @'   => [Network::Mastodon, '/houses'],
            'mastodon prefixed'       => [Network::Mastodon, '/x/@houses'],
            'mastodon nested'         => [Network::Mastodon, '/@houses/1'],
            'mastodon with a newline' => [Network::Mastodon, "/@houses\n"],
        ];
    }

    /**
     * @return array<string, array{Network, string}>
     */
    public static function servedProvider(): array
    {
        return [
            'facebook'           => [Network::Facebook, 'facebook.com'],
            'facebook www'       => [Network::Facebook, 'www.facebook.com'],
            'facebook web'       => [Network::Facebook, 'web.facebook.com'],
            'facebook basic'     => [Network::Facebook, 'mbasic.facebook.com'],
            'facebook short'     => [Network::Facebook, 'fb.com'],
            'facebook short www' => [Network::Facebook, 'www.fb.com'],
            'instagram'          => [Network::Instagram, 'instagram.com'],
            'linkedin'           => [Network::LinkedIn, 'linkedin.com'],
            'linkedin country'   => [Network::LinkedIn, 'uk.linkedin.com'],
            'x'                  => [Network::X, 'x.com'],
            'twitter'            => [Network::X, 'twitter.com'],
            'youtube'            => [Network::YouTube, 'youtube.com'],
            'tiktok'             => [Network::TikTok, 'tiktok.com'],
            'pinterest'          => [Network::Pinterest, 'pinterest.com'],
            'pinterest uk'       => [Network::Pinterest, 'www.pinterest.co.uk'],
            'threads'            => [Network::Threads, 'threads.com'],
            'bluesky'            => [Network::Bluesky, 'bsky.app'],
            'mastodon'           => [Network::Mastodon, 'mastodon.social'],
            'mastodon subdomain' => [Network::Mastodon, 'social.example-site.org'],
        ];
    }

    /**
     * @return array<string, array{Network, string}>
     */
    public static function unservedProvider(): array
    {
        return [
            'facebook prefixed'       => [Network::Facebook, 'notfacebook.com'],
            'facebook suffixed'       => [Network::Facebook, 'facebook.com.evil.net'],
            'facebook short suffixed' => [Network::Facebook, 'fb.com.evil.net'],
            'facebook short prefixed' => [Network::Facebook, 'xfb.com'],
            'instagram prefixed'      => [Network::Instagram, 'evilinstagram.com'],
            'instagram suffixed'      => [Network::Instagram, 'instagram.com.evil.net'],
            'linkedin prefixed'       => [Network::LinkedIn, 'evil.net.linkedin.com.au'],
            'linkedin suffixed'       => [Network::LinkedIn, 'linkedin.com.evil.net'],
            'linkedin long prefix'    => [Network::LinkedIn, 'evil.linkedin.com'],
            'x prefixed'              => [Network::X, 'evilx.com'],
            'x suffixed'              => [Network::X, 'x.com.evil.net'],
            'youtube prefixed'        => [Network::YouTube, 'notyoutube.com'],
            'youtube suffixed'        => [Network::YouTube, 'youtube.com.evil.net'],
            'youtube short link'      => [Network::YouTube, 'youtu.be'],
            'tiktok prefixed'         => [Network::TikTok, 'nottiktok.com'],
            'tiktok suffixed'         => [Network::TikTok, 'tiktok.com.evil.net'],
            'pinterest prefixed'      => [Network::Pinterest, 'notpinterest.com'],
            'pinterest suffixed'      => [Network::Pinterest, 'pinterest.com.evil.net'],
            'threads prefixed'        => [Network::Threads, 'notthreads.com'],
            'threads suffixed'        => [Network::Threads, 'threads.com.evil.net'],
            'bluesky prefixed'        => [Network::Bluesky, 'notbsky.app'],
            'bluesky suffixed'        => [Network::Bluesky, 'bsky.app.evil.net'],
            'mastodon without a dot'  => [Network::Mastodon, 'localhost'],
            'mastodon with a port'    => [Network::Mastodon, '-bad.social'],
            'mastodon numeric end'    => [Network::Mastodon, 'mastodon.social1'],
            'mastodon with a newline' => [Network::Mastodon, "mastodon.social\n"],
            'bluesky with a newline'  => [Network::Bluesky, "bsky.app\n"],
        ];
    }

    #[Test]
    #[DataProvider('notAnAccountProvider')]
    public function findsNoAccountInAPathThatNamesNone(Network $network, string $path): void
    {
        static::assertNull(ProfileRules::account($network, $path));
    }

    #[Test]
    #[DataProvider('accountProvider')]
    public function findsTheAccountAPathNames(Network $network, string $path, string $account): void
    {
        static::assertSame($account, ProfileRules::account($network, $path));
    }

    #[Test]
    #[DataProvider('servedProvider')]
    public function knowsTheHostsANetworkServesProfilesFrom(Network $network, string $host): void
    {
        static::assertTrue(ProfileRules::serves($network, $host));
    }

    #[Test]
    public function needsTheServerForMastodon(): void
    {
        static::assertNull(ProfileRules::handlePath(Network::Mastodon, 'houses'));
    }

    #[Test]
    public function needsTheWholeAddressForLinkedIn(): void
    {
        static::assertNull(ProfileRules::handlePath(Network::LinkedIn, 'houses'));
    }

    #[Test]
    #[DataProvider('unservedProvider')]
    public function refusesHostsThatOnlyLookLikeTheNetwork(Network $network, string $host): void
    {
        static::assertFalse(ProfileRules::serves($network, $host));
    }

    #[Test]
    public function writesAMastodonAddressOnTheAccountsServer(): void
    {
        static::assertSame(
            'https://aus.social/@houses',
            ProfileRules::address(Network::Mastodon, 'aus.social', 'houses'),
        );
    }

    #[Test]
    public function writesOtherAddressesOnTheNetworksOwnHost(): void
    {
        static::assertSame(
            'https://www.instagram.com/houses/',
            ProfileRules::address(Network::Instagram, 'm.instagram.com', 'houses'),
        );
    }
}
