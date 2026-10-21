<?php

declare(strict_types=1);

namespace Contenir\Social\Internal;

use Contenir\Social\Network;

use function preg_match;
use function sprintf;
use function strtolower;

/**
 * What a profile address looks like on each network: the hosts it is
 * served from, the paths that name an account, and where the canonical
 * address lives.
 *
 * @internal
 */
final class ProfileRules
{
    /**
     * The account a path names, or null when it names none.
     */
    public static function account(Network $network, string $path): ?string
    {
        $matches = [];
        if (1 !== preg_match(self::path($network), $path, $matches)) {
            return null;
        }

        $account = $matches['handle'] ?? '';

        return Network::Bluesky === $network ? strtolower($account) : $account;
    }

    /**
     * The canonical address of $account, served from $host.
     */
    public static function address(Network $network, string $host, string $account): string
    {
        return sprintf(self::canonical($network), Network::Mastodon === $network ? $host : '', $account);
    }

    /**
     * The path for an account given on its own, or null when the network
     * needs the whole address.
     */
    public static function handlePath(Network $network, string $handle): ?string
    {
        $path = match ($network) {
            Network::LinkedIn, Network::Mastodon                => null,
            Network::YouTube, Network::TikTok, Network::Threads => '/@%s',
            Network::Bluesky                                    => '/profile/%s',
            default                                             => '/%s',
        };

        return null === $path ? null : sprintf($path, $handle);
    }

    /**
     * Whether $host serves the network's profiles. Mastodon is any server.
     */
    public static function serves(Network $network, string $host): bool
    {
        $pattern = match ($network) {
            Network::Facebook  => '/^(?:(?:www|m|web|mbasic)\.)?facebook\.com$|^(?:www\.)?fb\.com$/D',
            Network::Instagram => '/^(?:www\.)?instagram\.com$/D',
            Network::LinkedIn  => '/^(?:[a-z]{2,3}\.)?linkedin\.com$/D',
            Network::X         => '/^(?:(?:www|mobile)\.)?(?:x|twitter)\.com$/D',
            Network::YouTube   => '/^(?:(?:www|m)\.)?youtube\.com$/D',
            Network::TikTok    => '/^(?:(?:www|m)\.)?tiktok\.com$/D',
            Network::Pinterest
                => '/^(?:[a-z]{2,3}\.)?pinterest\.(?:com|com\.au|co\.uk|ca|de|fr|es|it|jp|com\.mx|nz|ie|at|ch|dk|se|pt|cl|co\.kr|ph|nl|be|fi|no|pl|cz)$/D',
            Network::Threads   => '/^(?:www\.)?threads\.(?:com|net)$/D',
            Network::Bluesky   => '/^bsky\.app$/D',
            Network::Mastodon  => '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/D',
            Network::Vimeo     => '/^(?:www\.)?vimeo\.com$/D',
            Network::Behance   => '/^(?:www\.)?behance\.net$/D',
        };

        return 1 === preg_match($pattern, $host);
    }

    /**
     * The canonical address, with the host (Mastodon only) and the account
     * in place of the two %s.
     */
    private static function canonical(Network $network): string
    {
        return match ($network) {
            Network::Facebook  => 'https://www.facebook.com%s/%s',
            Network::Instagram => 'https://www.instagram.com%s/%s/',
            Network::LinkedIn  => 'https://www.linkedin.com%s/%s/',
            Network::X         => 'https://x.com%s/%s',
            Network::YouTube   => 'https://www.youtube.com%s/%s',
            Network::TikTok    => 'https://www.tiktok.com%s/@%s',
            Network::Pinterest => 'https://www.pinterest.com%s/%s/',
            Network::Threads   => 'https://www.threads.com%s/@%s',
            Network::Bluesky   => 'https://bsky.app%s/profile/%s',
            Network::Mastodon  => 'https://%s/@%s',
            Network::Vimeo     => 'https://vimeo.com%s/%s',
            Network::Behance   => 'https://www.behance.net%s/%s',
        };
    }

    /**
     * The paths that name an account, with the account as "handle".
     */
    private static function path(Network $network): string
    {
        return match ($network) {
            Network::Facebook
                => '~^/(?<handle>(?!(?:sharer|share|dialog|login|plugins|home|profile)(?:\.php)?(?:/|$))[A-Za-z0-9.\-]+(?:/[A-Za-z0-9.\-]+)*)/?$~D',
            Network::Instagram
                => '~^/(?<handle>(?!(?:p|reels?|stories|explore|accounts|direct)/?$)[A-Za-z0-9._]{1,30})/?$~D',
            Network::LinkedIn  => '~^/(?<handle>(?:company|in|school|showcase|groups)/[^/\s]+)/?$~D',
            Network::X
                => '~^/(?<handle>(?!(?:home|intent|share|search|i|explore|settings|hashtag|messages|notifications|login|signup|tos|privacy)/?$)[A-Za-z0-9_]{1,15})/?$~iD',
            Network::YouTube
                => '~^/(?<handle>@[A-Za-z0-9._\-]{3,30}|channel/UC[A-Za-z0-9_\-]{22}|c/[^/\s]+|user/[^/\s]+)/?$~D',
            Network::TikTok    => '~^/@(?<handle>[A-Za-z0-9._]{2,24})/?$~D',
            Network::Pinterest
                => '~^/(?<handle>(?!(?:pin|search|ideas|today|business|categories)/?$)[A-Za-z0-9_]{3,30})/?$~D',
            Network::Threads   => '~^/@(?<handle>[A-Za-z0-9._]{1,30})/?$~D',
            Network::Bluesky
                => '~^/profile/(?<handle>did:(?:plc|web):[A-Za-z0-9._:%\-]+|(?:[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?\.)+[A-Za-z][A-Za-z0-9\-]*)/?$~D',
            Network::Mastodon  => '~^/@(?<handle>[A-Za-z0-9_]{1,30})/?$~D',
            Network::Vimeo
                => '~^/(?<handle>channels/[A-Za-z0-9_\-]+|(?!(?:channels|groups|ondemand|showcase|album|categories|watch|search|join|log_in|upload|blog|features|help)/?$)[A-Za-z][A-Za-z0-9_\-]*)/?$~D',
            Network::Behance
                => '~^/(?<handle>(?!(?:gallery|galleries|search|joblist|assets|live|hire|blog|about|onboarding)/?$)[A-Za-z0-9_\-]+)/?$~D',
        };
    }
}
