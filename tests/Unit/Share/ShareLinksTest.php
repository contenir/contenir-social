<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Share;

use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLink;
use Contenir\Social\Share\ShareLinks;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;

#[Group('unit')]
final class ShareLinksTest extends TestCase
{
    private const string URL = 'https://example.com/houses?year=2026';

    /**
     * @return array<string, array{ShareTarget, string}>
     */
    public static function hrefProvider(): array
    {
        $url = 'https%3A%2F%2Fexample.com%2Fhouses%3Fyear%3D2026';

        return [
            'facebook'  => [ShareTarget::Facebook, "https://www.facebook.com/sharer/sharer.php?u={$url}"],
            'x'         => [ShareTarget::X, "https://x.com/intent/tweet?text=Houses%20%26%20Homes&url={$url}"],
            'linkedin'  => [ShareTarget::LinkedIn, "https://www.linkedin.com/sharing/share-offsite/?url={$url}"],
            'whatsapp'  => [ShareTarget::WhatsApp, "https://wa.me/?text=Houses%20%26%20Homes%20{$url}"],
            'email'     => [
                ShareTarget::Email,
                "mailto:?subject=Houses%20%26%20Homes&body=The%20winners%0A%0A{$url}",
            ],
            'bluesky'   => [ShareTarget::Bluesky, "https://bsky.app/intent/compose?text=Houses%20%26%20Homes%20{$url}"],
            'threads'   => [
                ShareTarget::Threads,
                "https://www.threads.com/intent/post?text=Houses%20%26%20Homes&url={$url}",
            ],
            'reddit'    => [ShareTarget::Reddit, "https://www.reddit.com/submit?url={$url}&title=Houses%20%26%20Homes"],
            'pinterest' => [
                ShareTarget::Pinterest,
                "https://www.pinterest.com/pin/create/button/?url={$url}"
                    . '&media=https%3A%2F%2Fexample.com%2Fa.jpg&description=Houses%20%26%20Homes',
            ],
            'telegram'  => [ShareTarget::Telegram, "https://t.me/share/url?url={$url}&text=Houses%20%26%20Homes"],
            'copy link' => [ShareTarget::CopyLink, self::URL],
            'share'     => [ShareTarget::Share, self::URL],
        ];
    }

    /**
     * @return array<string, array{ShareTarget, string}>
     */
    public static function untitledHrefProvider(): array
    {
        $url = 'https%3A%2F%2Fexample.com%2F';

        return [
            'x'        => [ShareTarget::X, "https://x.com/intent/tweet?url={$url}"],
            'whatsapp' => [ShareTarget::WhatsApp, "https://wa.me/?text={$url}"],
            'email'    => [ShareTarget::Email, "mailto:?body={$url}"],
            'bluesky'  => [ShareTarget::Bluesky, "https://bsky.app/intent/compose?text={$url}"],
        ];
    }

    #[Test]
    public function describesEachLink(): void
    {
        $link = (new ShareLinks(new ShareConfig([ShareTarget::CopyLink])))->for(
            new Page('https://example.com/', 'Houses'),
        )[0];

        static::assertSame(
            [ShareTarget::CopyLink, 'https://example.com/', 'Houses', 'Copy link', true],
            [$link->target, $link->url, $link->title, $link->label(), $link->isButton()],
        );
    }

    #[Test]
    public function leavesOutPinterestForAPageWithoutAnImage(): void
    {
        $links = (new ShareLinks(new ShareConfig([ShareTarget::Pinterest, ShareTarget::X])))->for(
            new Page('https://example.com/'),
        );

        static::assertSame(
            [ShareTarget::X],
            array_map(static fn(ShareLink $link): ShareTarget => $link->target, $links),
        );
    }

    #[Test]
    #[DataProvider('untitledHrefProvider')]
    public function leavesOutWhatAnUntitledPageLacks(ShareTarget $target, string $href): void
    {
        $links = (new ShareLinks(new ShareConfig([$target])))->for(new Page('https://example.com/'));

        static::assertSame($href, $links[0]->href);
    }

    #[Test]
    #[DataProvider('hrefProvider')]
    public function linksToEachNetworksShareAddress(ShareTarget $target, string $href): void
    {
        $links = (new ShareLinks(new ShareConfig([$target])))->for(new Page(
            self::URL,
            'Houses & Homes',
            'The winners',
            'https://example.com/a.jpg',
        ));

        static::assertSame([$href], array_map(static fn(ShareLink $link): string => $link->href, $links));
    }

    #[Test]
    public function offersTheDefaultButtonsWithoutAConfig(): void
    {
        $links = (new ShareLinks())->for(new Page('https://example.com/'));

        static::assertSame(
            ShareConfig::DEFAULT_TARGETS,
            array_map(static fn(ShareLink $link): ShareTarget => $link->target, $links),
        );
    }

    #[Test]
    public function sharesTheTaggedAddress(): void
    {
        $links = (new ShareLinks(new ShareConfig([ShareTarget::Facebook], utm: true)))->for(
            new Page('https://example.com/'),
        );

        static::assertSame(
            'https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fexample.com%2F%3Futm_source%3Dfacebook'
                . '%26utm_medium%3Dsocial%26utm_campaign%3Dshare',
            $links[0]->href,
        );
    }

    #[Test]
    public function tagsAnAddressWithAQueryAndFragment(): void
    {
        static::assertSame(
            'https://example.com/?a=1&utm_source=email&utm_medium=email&utm_campaign=share#winners',
            ShareLinks::tag('https://example.com/?a=1#winners', ShareTarget::Email),
        );
    }

    #[Test]
    public function tagsSharedAddressesWhenTheSiteAsks(): void
    {
        $links = (new ShareLinks(new ShareConfig([ShareTarget::LinkedIn, ShareTarget::CopyLink], utm: true)))->for(
            new Page('https://example.com/houses'),
        );

        static::assertSame(
            [
                'https://example.com/houses?utm_source=linkedin&utm_medium=social&utm_campaign=share',
                'https://example.com/houses?utm_source=copy&utm_medium=social&utm_campaign=share',
            ],
            array_map(static fn(ShareLink $link): string => $link->url, $links),
        );
    }
}
