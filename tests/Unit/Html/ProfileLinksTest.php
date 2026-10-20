<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Html;

use Contenir\Social\Html\IconSvg;
use Contenir\Social\Html\Options;
use Contenir\Social\Html\ProfileLinks;
use Contenir\Social\Icon\Icon;
use Contenir\Social\Network;
use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function strpos;

#[Group('unit')]
final class ProfileLinksTest extends TestCase
{
    private static function instagram(): Profiles
    {
        return new Profiles([ProfileUrl::parse(Network::Instagram, '@houses')]);
    }

    #[Test]
    public function escapesWhatItWrites(): void
    {
        static::assertStringContainsString(
            '<ul class="a&quot;b a&quot;b--profiles">',
            ProfileLinks::render(self::instagram(), new Options(block: 'a"b')),
        );
    }

    #[Test]
    public function namesItsClassesAfterTheSitesBlock(): void
    {
        static::assertSame(
            '<ul class="footer footer--profiles footer__social"><li class="footer__item footer__item--x">'
                . '<a class="footer__link" href="https://x.com/houses" rel="me noopener" target="_blank"'
                . ' aria-label="X (opens in a new tab)">'
                . IconSvg::render(Icon::X, 'footer__icon')
                . '</a></li></ul>',
            ProfileLinks::render(
                new Profiles([ProfileUrl::parse(Network::X, '@houses')]),
                new Options(
                    block: 'footer',
                    class: 'footer__social',
                ),
            ),
        );
    }

    #[Test]
    public function opensInTheSameTabWhenAsked(): void
    {
        static::assertStringContainsString(
            'href="https://www.instagram.com/houses/" rel="me noopener" aria-label="Instagram">',
            ProfileLinks::render(self::instagram(), new Options(newTab: false)),
        );
    }

    #[Test]
    public function writesIconsWithTheNetworkAsTheLinksName(): void
    {
        static::assertSame(
            '<ul class="social social--profiles"><li class="social__item social__item--instagram">'
                . '<a class="social__link" href="https://www.instagram.com/houses/" rel="me noopener" target="_blank"'
                . ' aria-label="Instagram (opens in a new tab)">'
                . IconSvg::render(Icon::Instagram)
                . '</a></li></ul>',
            ProfileLinks::render(self::instagram()),
        );
    }

    #[Test]
    public function writesNamesAloneWithoutIcons(): void
    {
        static::assertStringContainsString(
            'aria-label="Instagram (opens in a new tab)"><span class="social__label">Instagram</span></a>',
            ProfileLinks::render(self::instagram(), new Options(icons: false)),
        );
    }

    #[Test]
    public function writesNamesBesideIconsWhenAsked(): void
    {
        static::assertStringContainsString(
            IconSvg::render(Icon::Instagram) . '<span class="social__label">Instagram</span></a>',
            ProfileLinks::render(self::instagram(), new Options(labels: true)),
        );
    }

    #[Test]
    public function writesNothingWithoutProfiles(): void
    {
        static::assertSame('', ProfileLinks::render(new Profiles()));
    }

    #[Test]
    public function writesProfilesInTheOrderSitesListThem(): void
    {
        $html = ProfileLinks::render(new Profiles([
            ProfileUrl::parse(Network::Mastodon, '@houses@aus.social'),
            ProfileUrl::parse(Network::Facebook, '@houses'),
        ]));

        static::assertLessThan(
            (int) strpos($html, needle: 'social__item--mastodon'),
            (int) strpos($html, needle: 'social__item--facebook'),
        );
    }
}
