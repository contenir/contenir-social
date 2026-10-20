<?php

declare(strict_types=1);

namespace Contenir\Social;

use Contenir\Social\Internal\SettingsArray;

use function array_key_exists;
use function array_map;
use function array_values;

/**
 * A site's profiles, one per network at most, in the order sites list them.
 *
 * @api
 */
final readonly class Profiles
{
    /**
     * @var array<value-of<Network>, ProfileUrl>
     */
    private array $profiles;

    /**
     * @param list<ProfileUrl>      $profiles
     * @param array<string, string> $problems What was wrong with each stored value left out, by setting key
     */
    public function __construct(
        array $profiles = [],
        public array $problems = [],
    ) {
        $byNetwork = [];
        foreach ($profiles as $profile) {
            $byNetwork[$profile->network->value] = $profile;
        }

        $ordered = [];
        foreach (Network::cases() as $network) {
            if (! array_key_exists($network->value, $byNetwork)) {
                continue;
            }

            $ordered[$network->value] = $byNetwork[$network->value];
        }

        $this->profiles = $ordered;
    }

    /**
     * Reads the profiles from a site's settings, as nested arrays
     * (['social' => ['social_instagram' => '…']]). An account stored on its
     * own, as the CMS's Social Media Links plugin stored Instagram, X and
     * Pinterest, is read as its address; a value that isn't a profile is left
     * out and its problem kept.
     *
     * @param array<array-key, mixed> $settings
     */
    public static function fromSettings(array $settings): self
    {
        $values   = new SettingsArray($settings);
        $profiles = [];
        $problems = [];
        foreach (Network::cases() as $network) {
            $value = $values->text($network->settingKey());
            if ('' === $value) {
                continue;
            }

            $profile = ProfileUrl::tryParse($network, $value);
            if (null === $profile) {
                $problems[$network->settingKey()] = (string) ProfileUrl::problem($network, $value);
                continue;
            }

            $profiles[] = $profile;
        }

        return new self($profiles, $problems);
    }

    /**
     * Every profile, in the order sites list them.
     *
     * @return list<ProfileUrl>
     */
    public function all(): array
    {
        return array_values($this->profiles);
    }

    /**
     * The profile on $network, or null when the site has none there.
     */
    public function get(Network $network): ?ProfileUrl
    {
        return $this->profiles[$network->value] ?? null;
    }

    /**
     * Every profile's address, for schema.org's sameAs.
     *
     * @return list<string>
     */
    public function sameAs(): array
    {
        return array_map(static fn(ProfileUrl $profile): string => $profile->url, $this->all());
    }

    /**
     * The X account as twitter:site writes it (@yourname), or null when the
     * site has none.
     */
    public function twitterSite(): ?string
    {
        return $this->get(Network::X)?->mention();
    }
}
