<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Html;

use Contenir\Social\Html\Escape;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class EscapeTest extends TestCase
{
    #[Test]
    public function escapesMarkupAndBothQuotesForHtml5(): void
    {
        static::assertSame('&lt;a href=&quot;x&quot;&gt;Houses&apos; &amp;', Escape::html('<a href="x">Houses\' &'));
    }

    #[Test]
    public function replacesTextThatIsNotUtf8(): void
    {
        static::assertSame("Houses \u{FFFD}", Escape::html("Houses \xC3"));
    }
}
