<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Internal;

use Contenir\Social\Internal\SettingsArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SettingsArrayTest extends TestCase
{
    /**
     * @return array<string, array{mixed, bool, bool}>
     */
    public static function flagProvider(): array
    {
        return [
            'true'               => [true, false, true],
            'false'              => [false, true, false],
            'one'                => ['1', false, true],
            'the number one'     => [1, false, true],
            'yes, padded'        => [' Yes ', false, true],
            'on'                 => ['on', false, true],
            'true as text'       => ['TRUE', false, true],
            'zero'               => ['0', true, false],
            'the number zero'    => [0, true, false],
            'no'                 => ['no', true, false],
            'off'                => ['off', true, false],
            'false as text'      => ['false', true, false],
            'empty'              => ['', true, false],
            'missing, default'   => [null, true, true],
            'unreadable default' => ['maybe', true, true],
            'a list, default'    => [['1'], false, false],
        ];
    }

    #[Test]
    public function findsANestedValue(): void
    {
        static::assertSame(['x'], (new SettingsArray(['a' => ['b' => ['x']]]))->find('a.b'));
    }

    #[Test]
    public function findsANullThatIsStored(): void
    {
        static::assertNull((new SettingsArray(['a' => ['b' => null]]))->find('a.b'));
    }

    #[Test]
    public function findsNothingForAMissingKey(): void
    {
        static::assertNull((new SettingsArray(['a' => []]))->find('a.b'));
    }

    #[Test]
    public function findsNothingUnderAValueThatIsNotAnArray(): void
    {
        static::assertNull((new SettingsArray(['a' => 'text']))->find('a.b'));
    }

    #[Test]
    public function readsAnythingButTextAsEmpty(): void
    {
        static::assertSame('', (new SettingsArray(['a' => 12]))->text('a'));
    }

    #[Test]
    #[DataProvider('flagProvider')]
    public function readsASwitch(mixed $value, bool $default, bool $expected): void
    {
        static::assertSame($expected, (new SettingsArray(['a' => ['b' => $value]]))->flag('a.b', $default));
    }

    #[Test]
    public function readsTextTrimmed(): void
    {
        static::assertSame('houses', (new SettingsArray(['a' => ' houses ']))->text('a'));
    }
}
