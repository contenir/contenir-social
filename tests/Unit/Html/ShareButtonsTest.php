<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Html;

use Contenir\Social\Html\IconSvg;
use Contenir\Social\Html\Options;
use Contenir\Social\Html\ShareButtons;
use Contenir\Social\Icon\Icon;
use Contenir\Social\Share\ShareLink;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function substr_count;

#[Group('unit')]
final class ShareButtonsTest extends TestCase
{
    private const string URL = 'https://example.com/?a=1&b=2';

    private static function link(ShareTarget $target, string $title = 'Houses & "Homes"'): ShareLink
    {
        return new ShareLink($target, "https://share.example/?u={$target->value}&t=1", self::URL, $title);
    }

    #[Test]
    public function leavesOutAnEmptyTitle(): void
    {
        static::assertStringContainsString(
            'data-social-share="https://example.com/?a=1&amp;b=2" aria-label="Share…">',
            ShareButtons::render([self::link(ShareTarget::Share, '')]),
        );
    }

    #[Test]
    public function namesItsClassesAfterTheSitesBlock(): void
    {
        $html = ShareButtons::render(
            [self::link(ShareTarget::CopyLink)],
            new Options(
                block: 'post-share',
                class: 'post__share',
            ),
        );

        static::assertSame(
            [1, 1, 1, 1, 1],
            [
                substr_count($html, needle: '<ul class="post-share post-share--share post__share">'),
                substr_count($html, needle: 'class="post-share__item post-share__item--copy"'),
                substr_count($html, needle: 'class="post-share__button"'),
                substr_count($html, needle: 'class="post-share__icon"'),
                substr_count($html, needle: 'class="post-share__status"'),
            ],
        );
    }

    #[Test]
    public function opensNetworksInTheSameTabWhenAsked(): void
    {
        static::assertStringContainsString(
            'href="https://share.example/?u=x&amp;t=1" rel="noopener noreferrer" aria-label="Share on X">',
            ShareButtons::render([self::link(ShareTarget::X)], new Options(newTab: false)),
        );
    }

    #[Test]
    public function writesANetworkAsALinkThatOpensANewTab(): void
    {
        static::assertSame(
            '<ul class="social social--share"><li class="social__item social__item--x"><a class="social__link"'
                . ' href="https://share.example/?u=x&amp;t=1" target="_blank" rel="noopener noreferrer"'
                . ' aria-label="Share on X (opens in a new tab)">'
                . IconSvg::render(Icon::X)
                . '<span class="social__label">X</span></a></li></ul>',
            ShareButtons::render([self::link(ShareTarget::X)]),
        );
    }

    #[Test]
    public function writesCopyLinkAsAHiddenButtonWithItsStatus(): void
    {
        static::assertSame(
            '<ul class="social social--share"><li class="social__item social__item--copy" hidden>'
                . '<button class="social__button" type="button" data-social-copy="https://example.com/?a=1&amp;b=2"'
                . ' aria-label="Copy link">'
                . IconSvg::render(Icon::Link)
                . '<span class="social__label">Copy link</span></button>'
                . '<span class="social__status" role="status" data-social-copied="Copied"></span></li></ul>',
            ShareButtons::render([self::link(ShareTarget::CopyLink)]),
        );
    }

    #[Test]
    public function writesEmailAsALinkInTheSameTab(): void
    {
        static::assertStringContainsString(
            '<a class="social__link" href="https://share.example/?u=email&amp;t=1" rel="noopener noreferrer"'
                . ' aria-label="Share by email">',
            ShareButtons::render([self::link(ShareTarget::Email)]),
        );
    }

    #[Test]
    public function writesIconsAloneWhenAsked(): void
    {
        static::assertStringContainsString(
            'aria-label="Share on X (opens in a new tab)">' . IconSvg::render(Icon::X) . '</a>',
            ShareButtons::render([self::link(ShareTarget::X)], new Options(labels: false)),
        );
    }

    #[Test]
    public function writesNamesAloneWithoutIcons(): void
    {
        static::assertStringContainsString(
            'aria-label="Copy link"><span class="social__label">Copy link</span></button>',
            ShareButtons::render([self::link(ShareTarget::CopyLink)], new Options(icons: false)),
        );
    }

    #[Test]
    public function writesNothingWithoutButtons(): void
    {
        static::assertSame('', ShareButtons::render([]));
    }

    #[Test]
    public function writesShareAsAHiddenButtonWithTheTitle(): void
    {
        static::assertSame(
            '<ul class="social social--share"><li class="social__item social__item--share" hidden>'
                . '<button class="social__button" type="button" data-social-share="https://example.com/?a=1&amp;b=2"'
                . ' data-social-title="Houses &amp; &quot;Homes&quot;" aria-label="Share…">'
                . IconSvg::render(Icon::Share)
                . '<span class="social__label">Share…</span></button></li></ul>',
            ShareButtons::render([self::link(ShareTarget::Share)]),
        );
    }
}
