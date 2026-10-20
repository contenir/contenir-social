<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit;

use Contenir\Social\Exception\InvalidProfileException;
use Contenir\Social\Network;
use Contenir\Social\ProfileUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ProfileUrlTest extends TestCase
{
    /**
     * @return array<string, array{Network, string, string, string}>
     */
    public static function moreProfileProvider(): array
    {
        return [
            'linkedin company'                => [
                Network::LinkedIn,
                'https://www.linkedin.com/company/houses-awards',
                'https://www.linkedin.com/company/houses-awards/',
                'company/houses-awards',
            ],
            'linkedin person, country host'   => [
                Network::LinkedIn,
                'https://au.linkedin.com/in/jane-citizen/',
                'https://www.linkedin.com/in/jane-citizen/',
                'in/jane-citizen',
            ],
            'linkedin school'                 => [
                Network::LinkedIn,
                'linkedin.com/school/unisa',
                'https://www.linkedin.com/school/unisa/',
                'school/unisa',
            ],
            'linkedin showcase'               => [
                Network::LinkedIn,
                'https://www.linkedin.com/showcase/houses',
                'https://www.linkedin.com/showcase/houses/',
                'showcase/houses',
            ],
            'linkedin group'                  => [
                Network::LinkedIn,
                'https://www.linkedin.com/groups/12345',
                'https://www.linkedin.com/groups/12345/',
                'groups/12345',
            ],
            'x account'                       => [
                Network::X,
                '@HousesMag',
                'https://x.com/HousesMag',
                'HousesMag',
            ],
            'x from a twitter address'        => [
                Network::X,
                'https://twitter.com/HousesMag/',
                'https://x.com/HousesMag',
                'HousesMag',
            ],
            'x from a mobile twitter address' => [
                Network::X,
                'https://mobile.twitter.com/houses_mag',
                'https://x.com/houses_mag',
                'houses_mag',
            ],
            'x address'                       => [
                Network::X,
                'https://www.x.com/HousesMag',
                'https://x.com/HousesMag',
                'HousesMag',
            ],
            'youtube handle'                  => [
                Network::YouTube,
                '@housesmagazine',
                'https://www.youtube.com/@housesmagazine',
                '@housesmagazine',
            ],
            'youtube handle without an @'     => [
                Network::YouTube,
                'houses-magazine',
                'https://www.youtube.com/@houses-magazine',
                '@houses-magazine',
            ],
            'youtube channel'                 => [
                Network::YouTube,
                'https://m.youtube.com/channel/UCabcdefghijklmnopqrstuv/',
                'https://www.youtube.com/channel/UCabcdefghijklmnopqrstuv',
                'channel/UCabcdefghijklmnopqrstuv',
            ],
            'youtube custom address'          => [
                Network::YouTube,
                'https://www.youtube.com/c/HousesMagazine',
                'https://www.youtube.com/c/HousesMagazine',
                'c/HousesMagazine',
            ],
            'youtube user'                    => [
                Network::YouTube,
                'https://youtube.com/user/housesmag',
                'https://www.youtube.com/user/housesmag',
                'user/housesmag',
            ],
            'tiktok account'                  => [
                Network::TikTok,
                '@houses.mag',
                'https://www.tiktok.com/@houses.mag',
                'houses.mag',
            ],
            'tiktok address'                  => [
                Network::TikTok,
                'https://m.tiktok.com/@houses_mag/',
                'https://www.tiktok.com/@houses_mag',
                'houses_mag',
            ],
            'pinterest account'               => [
                Network::Pinterest,
                'housesmag',
                'https://www.pinterest.com/housesmag/',
                'housesmag',
            ],
            'pinterest australian address'    => [
                Network::Pinterest,
                'https://www.pinterest.com.au/housesmag',
                'https://www.pinterest.com/housesmag/',
                'housesmag',
            ],
            'pinterest country host'          => [
                Network::Pinterest,
                'https://au.pinterest.com/houses_mag/',
                'https://www.pinterest.com/houses_mag/',
                'houses_mag',
            ],
            'threads account'                 => [
                Network::Threads,
                '@houses.mag',
                'https://www.threads.com/@houses.mag',
                'houses.mag',
            ],
            'threads old address'             => [
                Network::Threads,
                'https://www.threads.net/@houses_mag',
                'https://www.threads.com/@houses_mag',
                'houses_mag',
            ],
            'bluesky handle'                  => [
                Network::Bluesky,
                '@Houses.bsky.social',
                'https://bsky.app/profile/houses.bsky.social',
                'houses.bsky.social',
            ],
            'bluesky custom domain'           => [
                Network::Bluesky,
                'housesawards.com.au',
                'https://bsky.app/profile/housesawards.com.au',
                'housesawards.com.au',
            ],
            'bluesky address'                 => [
                Network::Bluesky,
                'https://bsky.app/profile/houses.bsky.social/',
                'https://bsky.app/profile/houses.bsky.social',
                'houses.bsky.social',
            ],
            'bluesky by did'                  => [
                Network::Bluesky,
                'https://bsky.app/profile/did:plc:abc234def567ghi',
                'https://bsky.app/profile/did:plc:abc234def567ghi',
                'did:plc:abc234def567ghi',
            ],
            'mastodon account'                => [
                Network::Mastodon,
                '@houses@Mastodon.Social',
                'https://mastodon.social/@houses',
                'houses',
            ],
            'mastodon address'                => [
                Network::Mastodon,
                'https://aus.social/@houses_mag/',
                'https://aus.social/@houses_mag',
                'houses_mag',
            ],
        ];
    }

    /**
     * @return array<string, array{Network, string, string}>
     */
    public static function problemProvider(): array
    {
        return [
            'another site'                        => [
                Network::Instagram,
                'https://evil.example/housesmagazine',
                'That address is on evil.example, not Instagram. Enter the profile\'s address, such as'
                    . ' https://www.instagram.com/yourname/.',
            ],
            'another network'                     => [
                Network::Facebook,
                'https://www.instagram.com/housesmagazine/',
                'That address is on www.instagram.com, not Facebook. Enter the profile\'s address, such as'
                    . ' https://www.facebook.com/yourpage.',
            ],
            'the network\'s home page'            => [
                Network::Instagram,
                'instagram.com',
                'That isn\'t a profile on Instagram. Enter its address, such as https://www.instagram.com/yourname/,'
                    . ' or @yourname.',
            ],
            'a post, not a profile'               => [
                Network::Instagram,
                'https://www.instagram.com/p/Cabc123/',
                'That isn\'t a profile on Instagram. Enter its address, such as https://www.instagram.com/yourname/,'
                    . ' or @yourname.',
            ],
            'not a web address'                   => [
                Network::X,
                'javascript://x.com/%0Aalert(1)',
                'That isn\'t a profile on X. Enter its address, such as https://x.com/yourname, or @yourname.',
            ],
            'an address with no host'             => [
                Network::X,
                'https://',
                'That isn\'t a profile on X. Enter its address, such as https://x.com/yourname, or @yourname.',
            ],
            'an x page that isn\'t one'           => [
                Network::X,
                'https://x.com/intent',
                'That isn\'t a profile on X. Enter its address, such as https://x.com/yourname, or @yourname.',
            ],
            'linkedin account'                    => [
                Network::LinkedIn,
                'houses-awards',
                'That isn\'t a profile on LinkedIn. Enter its address, such as'
                    . ' https://www.linkedin.com/company/yourname/.',
            ],
            'mastodon without a server'           => [
                Network::Mastodon,
                '@houses',
                'That isn\'t a profile on Mastodon. Enter its address, such as https://mastodon.social/@yourname,'
                    . ' or @yourname@mastodon.social.',
            ],
            'bluesky without a domain'            => [
                Network::Bluesky,
                'houses',
                'That isn\'t a profile on Bluesky. Enter its address, such as'
                    . ' https://bsky.app/profile/yourname.bsky.social, or yourname.bsky.social.',
            ],
            'facebook sharer'                     => [
                Network::Facebook,
                'https://www.facebook.com/sharer/sharer.php?u=x',
                'That isn\'t a profile on Facebook. Enter its address, such as https://www.facebook.com/yourpage,'
                    . ' or @yourname.',
            ],
            'facebook number with letters before' => [
                Network::Facebook,
                'https://www.facebook.com/profile.php?id=a123',
                'That isn\'t a profile on Facebook. Enter its address, such as https://www.facebook.com/yourpage,'
                    . ' or @yourname.',
            ],
            'facebook number with letters after'  => [
                Network::Facebook,
                'https://www.facebook.com/profile.php?id=123a',
                'That isn\'t a profile on Facebook. Enter its address, such as https://www.facebook.com/yourpage,'
                    . ' or @yourname.',
            ],
            'facebook number with a newline'      => [
                Network::Facebook,
                'https://www.facebook.com/profile.php?id=123%0A',
                'That isn\'t a profile on Facebook. Enter its address, such as https://www.facebook.com/yourpage,'
                    . ' or @yourname.',
            ],
            'mastodon without a user'             => [
                Network::Mastodon,
                '@@aus.social',
                'That isn\'t a profile on Mastodon. Enter its address, such as https://mastodon.social/@yourname,'
                    . ' or @yourname@mastodon.social.',
            ],
            'mastodon without a server name'      => [
                Network::Mastodon,
                '@houses@',
                'That isn\'t a profile on Mastodon. Enter its address, such as https://mastodon.social/@yourname,'
                    . ' or @yourname@mastodon.social.',
            ],
            'mastodon with two servers'           => [
                Network::Mastodon,
                '@houses@aus.social@x.social',
                'That isn\'t a profile on Mastodon. Enter its address, such as https://mastodon.social/@yourname,'
                    . ' or @yourname@mastodon.social.',
            ],
            'the network\'s home in capitals'     => [
                Network::Instagram,
                'INSTAGRAM.COM',
                'That isn\'t a profile on Instagram. Enter its address, such as https://www.instagram.com/yourname/,'
                    . ' or @yourname.',
            ],
            'an ftp address'                      => [
                Network::X,
                'ftp://x.com/HousesMag',
                'That isn\'t a profile on X. Enter its address, such as https://x.com/yourname, or @yourname.',
            ],
            'facebook number that is not'         => [
                Network::Facebook,
                'https://www.facebook.com/profile.php?id=abc',
                'That isn\'t a profile on Facebook. Enter its address, such as https://www.facebook.com/yourpage,'
                    . ' or @yourname.',
            ],
        ];
    }

    /**
     * @return array<string, array{Network, string, string, string}>
     */
    public static function profileProvider(): array
    {
        return [
            'facebook page'                  => [
                Network::Facebook,
                'https://www.facebook.com/housesmagazine',
                'https://www.facebook.com/housesmagazine',
                'housesmagazine',
            ],
            'facebook mobile, over http'     => [
                Network::Facebook,
                'http://m.facebook.com/houses.magazine/',
                'https://www.facebook.com/houses.magazine',
                'houses.magazine',
            ],
            'facebook without a scheme'      => [
                Network::Facebook,
                'facebook.com/housesmagazine',
                'https://www.facebook.com/housesmagazine',
                'housesmagazine',
            ],
            'facebook page with an id'       => [
                Network::Facebook,
                'https://www.facebook.com/pages/Houses/123456',
                'https://www.facebook.com/pages/Houses/123456',
                'pages/Houses/123456',
            ],
            'facebook profile by number'     => [
                Network::Facebook,
                'https://www.facebook.com/profile.php?id=100064',
                'https://www.facebook.com/profile.php?id=100064',
                'profile.php?id=100064',
            ],
            'facebook short host'            => [
                Network::Facebook,
                'https://fb.com/housesmagazine',
                'https://www.facebook.com/housesmagazine',
                'housesmagazine',
            ],
            'facebook account'               => [
                Network::Facebook,
                '@housesmagazine',
                'https://www.facebook.com/housesmagazine',
                'housesmagazine',
            ],
            'facebook drops tracking'        => [
                Network::Facebook,
                'https://www.facebook.com/housesmagazine?ref=bookmarks#about',
                'https://www.facebook.com/housesmagazine',
                'housesmagazine',
            ],
            'x with a link in the query'     => [
                Network::X,
                'x.com/HousesMag?ref=https://example.com/',
                'https://x.com/HousesMag',
                'HousesMag',
            ],
            'x starting with slashes'        => [
                Network::X,
                '//x.com/HousesMag',
                'https://x.com/HousesMag',
                'HousesMag',
            ],
            'facebook with an id it ignores' => [
                Network::Facebook,
                'https://www.facebook.com/housesmagazine?id=5',
                'https://www.facebook.com/housesmagazine',
                'housesmagazine',
            ],
            'instagram account'              => [
                Network::Instagram,
                'housesmagazine',
                'https://www.instagram.com/housesmagazine/',
                'housesmagazine',
            ],
            'instagram account with an @'    => [
                Network::Instagram,
                ' @houses.magazine_au ',
                'https://www.instagram.com/houses.magazine_au/',
                'houses.magazine_au',
            ],
            'instagram address'              => [
                Network::Instagram,
                'https://instagram.com/housesmagazine?igsh=abc',
                'https://www.instagram.com/housesmagazine/',
                'housesmagazine',
            ],
            'instagram in capitals'          => [
                Network::Instagram,
                'HTTPS://WWW.INSTAGRAM.COM/housesmagazine/',
                'https://www.instagram.com/housesmagazine/',
                'housesmagazine',
            ],
        ];
    }

    #[Test]
    #[DataProvider('problemProvider')]
    public function explainsWhyAValueIsNotAProfile(Network $network, string $value, string $problem): void
    {
        static::assertSame($problem, ProfileUrl::problem($network, $value));
    }

    #[Test]
    public function findsNoProblemWithAProfile(): void
    {
        static::assertNull(ProfileUrl::problem(Network::Instagram, '@housesmagazine'));
    }

    #[Test]
    public function findsNoProblemWithNoProfile(): void
    {
        static::assertNull(ProfileUrl::problem(Network::Instagram, '  '));
    }

    #[Test]
    public function mentionsAYouTubeHandleWithOneAt(): void
    {
        static::assertSame('@houses', ProfileUrl::parse(Network::YouTube, '@houses')->mention());
    }

    #[Test]
    public function mentionsTheAccountWithOneAt(): void
    {
        static::assertSame('@HousesMag', ProfileUrl::parse(Network::X, '@HousesMag')->mention());
    }

    #[Test]
    #[DataProvider('profileProvider')]
    #[DataProvider('moreProfileProvider')]
    public function readsAProfileAsItsCanonicalAddress(
        Network $network,
        string $value,
        string $url,
        string $account,
    ): void {
        $profile = ProfileUrl::parse($network, $value);

        static::assertSame([$network, $url, $account], [$profile->network, $profile->url, $profile->account]);
    }

    #[Test]
    public function refusesAValueThatIsNotAProfile(): void
    {
        $this->expectExceptionObject(InvalidProfileException::because(
            'That address is on example.com, not TikTok. Enter the profile\'s address, such as'
                . ' https://www.tiktok.com/@yourname.',
        ));

        ProfileUrl::parse(Network::TikTok, 'example.com/@houses');
    }

    #[Test]
    public function refusesNoProfile(): void
    {
        $this->expectExceptionObject(InvalidProfileException::because('Enter the Threads address.'));

        ProfileUrl::parse(Network::Threads, ' ');
    }

    #[Test]
    public function triesAProfile(): void
    {
        static::assertSame(
            'https://x.com/HousesMag',
            ProfileUrl::tryParse(Network::X, ' @HousesMag ')?->url,
        );
    }

    #[Test]
    public function triesAValueThatIsNotAProfile(): void
    {
        static::assertNull(ProfileUrl::tryParse(Network::X, 'https://x.com/search'));
    }

    #[Test]
    public function triesNoProfile(): void
    {
        static::assertNull(ProfileUrl::tryParse(Network::X, ''));
    }
}
