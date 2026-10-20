<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Script;

use Contenir\Social\Script\ShareScript;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ShareScriptTest extends TestCase
{
    #[Test]
    public function keepsTheSourceSafeToWriteInline(): void
    {
        static::assertStringNotContainsString('</', ShareScript::source());
    }

    #[Test]
    public function readsTheScriptFromTheResources(): void
    {
        static::assertStringEqualsFile(__DIR__ . '/../../../resources/share.js', ShareScript::source());
    }

    #[Test]
    public function writesNoNonceForAnEmptyOne(): void
    {
        static::assertSame('<script>' . ShareScript::source() . '</script>', ShareScript::tag(''));
    }

    #[Test]
    public function writesTheScriptWithoutANonce(): void
    {
        static::assertSame('<script>' . ShareScript::source() . '</script>', ShareScript::tag());
    }

    #[Test]
    public function writesTheScriptWithTheNonce(): void
    {
        static::assertSame(
            '<script nonce="a&quot;b">' . ShareScript::source() . '</script>',
            ShareScript::tag('a"b'),
        );
    }
}
