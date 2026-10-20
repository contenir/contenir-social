<?php

declare(strict_types=1);

namespace Contenir\Social\Share;

use Contenir\Social\Internal\SettingsArray;
use Contenir\Social\SettingKeys;

use function array_values;
use function explode;
use function is_array;
use function is_string;
use function trim;

/**
 * A site's share buttons: which, in what order, whether their names show
 * beside their icons, and whether shared addresses carry UTM tags.
 *
 * @api
 */
final readonly class ShareConfig
{
    /**
     * The buttons a site has until it chooses.
     */
    public const array DEFAULT_TARGETS = [
        ShareTarget::Facebook,
        ShareTarget::X,
        ShareTarget::LinkedIn,
        ShareTarget::Email,
        ShareTarget::CopyLink,
        ShareTarget::Share,
    ];

    /**
     * @param list<ShareTarget> $targets In order
     * @param bool              $labels  Show each button's name beside its icon
     * @param bool              $utm     Tag shared addresses with utm_source, utm_medium and utm_campaign
     */
    public function __construct(
        public array $targets = self::DEFAULT_TARGETS,
        public bool $labels = true,
        public bool $utm = false,
    ) {}

    /**
     * Reads the share buttons from a site's settings, as nested arrays. The
     * buttons may be stored as a list of names, as the CMS's repeating rows
     * ([['fields' => ['network' => 'x']], …]) or as names separated by
     * commas. Names that aren't share targets are left out, and a repeat
     * keeps the place it first had. A site that has chosen none has
     * DEFAULT_TARGETS: to show no buttons, a site leaves share() out.
     *
     * @param array<array-key, mixed> $settings
     */
    public static function fromSettings(array $settings): self
    {
        $values  = new SettingsArray($settings);
        $targets = self::targets($values->find(SettingKeys::SHARE_NETWORKS));

        return new self(
            [] === $targets ? self::DEFAULT_TARGETS : $targets,
            $values->flag(SettingKeys::SHARE_LABELS, default: true),
            $values->flag(SettingKeys::SHARE_UTM, default: false),
        );
    }

    /**
     * The share target a stored row names, or null.
     *
     * @mago-expect analysis:mixed-assignment A stored row is untyped; it is checked here.
     */
    private static function name(mixed $row): ?string
    {
        if (is_array($row)) {
            $fields = $row['fields'] ?? $row;

            return is_array($fields) && is_string($fields['network'] ?? null) ? $fields['network'] : null;
        }

        return is_string($row) ? $row : null;
    }

    /**
     * @return list<ShareTarget>
     */
    private static function targets(mixed $stored): array
    {
        $rows = match (true) {
            is_string($stored) => explode(',', $stored),
            is_array($stored)  => $stored,
            default            => [],
        };

        $targets = [];
        foreach ($rows as $row) {
            $target = ShareTarget::tryFrom(trim((string) self::name($row)));
            if (null !== $target) {
                $targets[$target->value] = $target;
            }
        }

        return array_values($targets);
    }
}
