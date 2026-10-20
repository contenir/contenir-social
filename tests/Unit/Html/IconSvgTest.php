<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Html;

use Contenir\Social\Html\IconSvg;
use Contenir\Social\Icon\Icon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class IconSvgTest extends TestCase
{
    #[Test]
    public function drawsAnIconHiddenFromAssistiveTechnology(): void
    {
        static::assertSame(
            '<svg class="social__icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"'
                . ' aria-hidden="true" focusable="false"><path d="'
                . Icon::X->paths()[0]
                . '"/></svg>',
            IconSvg::render(Icon::X),
        );
    }

    #[Test]
    public function drawsEveryPathOfAnIconWithAnEscapedClass(): void
    {
        [$first, $second] = Icon::Email->paths();

        static::assertSame(
            '<svg class="nav&quot;icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"'
                . " aria-hidden=\"true\" focusable=\"false\"><path d=\"{$first}\"/><path d=\"{$second}\"/></svg>",
            IconSvg::render(Icon::Email, 'nav"icon'),
        );
    }
}
