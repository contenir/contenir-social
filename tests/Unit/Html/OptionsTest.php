<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Html;

use Contenir\Social\Html\Options;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class OptionsTest extends TestCase
{
    /**
     * @return array<string, array{Options, bool, bool}>
     */
    public static function labelsProvider(): array
    {
        return [
            'left to the default, on'  => [new Options(), true, true],
            'left to the default, off' => [new Options(), false, false],
            'asked for'                => [new Options(labels: true), false, true],
            'turned off'               => [new Options(labels: false), true, false],
            'no icons'                 => [
                new Options(
                    icons: false,
                    labels: false,
                ),
                false,
                true,
            ],
        ];
    }

    #[Test]
    public function drawsIconsInANewTabInTheSocialBlockByDefault(): void
    {
        $options = new Options();

        static::assertSame([true, null, true, 'social', ''], [
            $options->icons,
            $options->labels,
            $options->newTab,
            $options->block,
            $options->class,
        ]);
    }

    #[Test]
    #[DataProvider('labelsProvider')]
    public function showsNamesWhenAskedOrWhenThereAreNoIcons(Options $options, bool $default, bool $shows): void
    {
        static::assertSame($shows, $options->showsLabels($default));
    }
}
