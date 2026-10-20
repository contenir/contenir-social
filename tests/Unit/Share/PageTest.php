<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Share;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Share\Page;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class PageTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function notAbsoluteProvider(): array
    {
        return [
            'a path'         => ['/houses/2026'],
            'no scheme'      => ['//example.com/houses'],
            'another scheme' => ['ftp://example.com/houses'],
            'no host'        => ['https:///houses'],
            'one slash'      => ['https:/houses'],
            'script'         => ['javascript:alert(1)'],
        ];
    }

    #[Test]
    public function acceptsAnHttpAddressInCapitals(): void
    {
        static::assertSame('HTTP://example.com/', (new Page('HTTP://example.com/'))->url);
    }

    #[Test]
    public function explainsAnAddressThatIsNotAbsolute(): void
    {
        static::assertSame(
            'A page to share needs an absolute http or https address; "/houses" isn\'t one',
            InvalidPageException::notAbsolute('address', '/houses')->getMessage(),
        );
    }

    #[Test]
    public function hasNoImageWhenGivenAnEmptyOne(): void
    {
        static::assertNull((new Page('https://example.com/', image: ' '))->image);
    }

    #[Test]
    public function hasNoTitleOrDescriptionUnlessGiven(): void
    {
        $page = new Page('https://example.com/');

        static::assertSame(['', '', null], [$page->title, $page->description, $page->image]);
    }

    #[Test]
    public function keepsItsMetadataTrimmed(): void
    {
        $page = new Page(
            ' https://example.com/houses ',
            ' Houses 2026 ',
            ' The winners ',
            ' https://example.com/a.jpg ',
        );

        static::assertSame(
            ['https://example.com/houses', 'Houses 2026', 'The winners', 'https://example.com/a.jpg'],
            [$page->url, $page->title, $page->description, $page->image],
        );
    }

    #[Test]
    #[DataProvider('notAbsoluteProvider')]
    public function refusesAnAddressThatIsNotAbsolute(string $url): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', $url));

        new Page($url);
    }

    #[Test]
    public function refusesAnImageThatIsNotAbsolute(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('image address', '/a.jpg'));

        new Page('https://example.com/', image: '/a.jpg');
    }
}
