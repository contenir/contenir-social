<?php

declare(strict_types=1);

namespace Contenir\Social\Internal;

use Contenir\Social\Network;

use function in_array;
use function is_array;
use function is_string;
use function ltrim;
use function parse_str;
use function parse_url;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function strtolower;
use function substr;

/**
 * Where a value an editor gave for a profile points: a host, a path and a
 * query. An account given on its own points at the network's own host,
 * left empty, or for Mastodon at the account's server.
 *
 * @internal
 */
final readonly class Location
{
    public function __construct(
        public string $host,
        public string $path,
        public string $query = '',
    ) {}

    /**
     * Where $value points, or null when it can't be read as an address or
     * an account.
     */
    public static function of(Network $network, string $value): ?self
    {
        if (! str_contains($value, '/') && ! ProfileRules::serves($network, strtolower($value))) {
            return Account::locate($network, str_starts_with($value, '@') ? substr($value, offset: 1) : $value);
        }

        $url = 1 === preg_match('~^[a-z][a-z0-9+.\-]*://~i', $value)
            ? $value
            : 'https://' . ltrim($value, characters: '/');
        $parts  = parse_url($url);
        $parts  = is_array($parts) ? $parts : [];
        $host   = strtolower($parts['host'] ?? '');
        $scheme = strtolower($parts['scheme'] ?? '');

        return '' !== $host && in_array($scheme, ['http', 'https'], strict: true)
            ? new self($host, $parts['path'] ?? '', $parts['query'] ?? '')
            : null;
    }

    /**
     * The account a Facebook profile with no username names
     * (profile.php?id=…), or null when it isn't one.
     */
    public function facebookId(): ?string
    {
        if ('/profile.php' !== $this->path) {
            return null;
        }

        $query = [];
        parse_str($this->query, $query);
        $id = $query['id'] ?? null;

        return is_string($id) && 1 === preg_match('/^\d+$/D', $id) ? "profile.php?id={$id}" : null;
    }
}
