<?php

declare(strict_types=1);

namespace Contenir\Social\Internal;

use Contenir\Social\Network;

use function count;
use function explode;
use function strtolower;

/**
 * An account an editor gave on its own, without its address: yourname on
 * most networks, yourname.bsky.social on Bluesky, yourname@server on
 * Mastodon.
 *
 * @internal
 */
final class Account
{
    /**
     * Where $handle points, or null when the network needs the whole
     * address. The host is left empty for the network's own.
     */
    public static function locate(Network $network, string $handle): ?Location
    {
        if (Network::Mastodon === $network) {
            $parts = explode('@', $handle);

            return 2 === count($parts) && '' !== $parts[0] && '' !== $parts[1]
                ? new Location(strtolower($parts[1]), "/@{$parts[0]}")
                : null;
        }

        $path = ProfileRules::handlePath($network, $handle);

        return null === $path ? null : new Location('', $path);
    }
}
