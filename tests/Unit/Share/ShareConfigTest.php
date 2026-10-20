<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Share;

use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ShareConfigTest extends TestCase
{
    /**
     * @return array<string, array{mixed, list<ShareTarget>}>
     */
    public static function storedTargetsProvider(): array
    {
        return [
            'a list of names'        => [
                ['x',            'email'],
                [ShareTarget::X, ShareTarget::Email],
            ],
            'repeating rows'         => [
                [['fields' => ['network' => 'linkedin']], ['fields' => ['network' => 'copy']]],
                [ShareTarget::LinkedIn, ShareTarget::CopyLink],
            ],
            'rows without fields'    => [[['network' => 'reddit']], [ShareTarget::Reddit]],
            'names with commas'      => [
                'whatsapp, telegram ,share',
                [
                    ShareTarget::WhatsApp,
                    ShareTarget::Telegram,
                    ShareTarget::Share,
                ],
            ],
            'unknown names'          => [['x', 'myspace', '', null, 3, ['fields' => 'x']], [ShareTarget::X]],
            'repeats keep the first' => [['email', 'x', 'email'], [ShareTarget::Email, ShareTarget::X]],
            'none chosen'            => [[], []],
            'something else'         => [12, []],
        ];
    }

    #[Test]
    public function offersTheDefaultButtonsUntilASiteChooses(): void
    {
        $config = ShareConfig::fromSettings([]);

        static::assertSame(
            [
                [
                    ShareTarget::Facebook,
                    ShareTarget::X,
                    ShareTarget::LinkedIn,
                    ShareTarget::Email,
                    ShareTarget::CopyLink,
                    ShareTarget::Share,
                ],
                true,
                false,
            ],
            [$config->targets, $config->labels, $config->utm],
        );
    }

    #[Test]
    public function readsLabelsAndUtmTags(): void
    {
        $config = ShareConfig::fromSettings([
            'extensions' => ['social' => [
                'share_labels' => '0',
                'share_utm'    => '1',
            ]],
        ]);

        static::assertSame([false, true], [$config->labels, $config->utm]);
    }

    #[Test]
    #[DataProvider('storedTargetsProvider')]
    public function readsTheButtonsAsStored(mixed $stored, array $targets): void
    {
        static::assertSame(
            $targets,
            ShareConfig::fromSettings(['extensions' => ['social' => ['share_networks' => $stored]]])->targets,
        );
    }

    #[Test]
    public function showsNamesAndLeavesAddressesUntaggedByDefault(): void
    {
        $config = new ShareConfig();

        static::assertSame([true, false], [$config->labels, $config->utm]);
    }
}
