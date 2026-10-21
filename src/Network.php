<?php

declare(strict_types=1);

namespace Contenir\Social;

use Contenir\Social\Icon\Icon;

/**
 * A social network a site can link its profile on, in the order sites list
 * them.
 *
 * @api
 */
enum Network: string
{
    case Facebook  = 'facebook';
    case Instagram = 'instagram';
    case LinkedIn  = 'linkedin';
    case X         = 'x';
    case YouTube   = 'youtube';
    case TikTok    = 'tiktok';
    case Pinterest = 'pinterest';
    case Threads   = 'threads';
    case Bluesky   = 'bluesky';
    case Mastodon  = 'mastodon';
    case Vimeo     = 'vimeo';
    case Behance   = 'behance';

    /**
     * An example of the profile address, for editors.
     */
    public function example(): string
    {
        return match ($this) {
            self::Facebook => 'https://www.facebook.com/yourpage',
            self::Instagram => 'https://www.instagram.com/yourname/',
            self::LinkedIn => 'https://www.linkedin.com/company/yourname/',
            self::X => 'https://x.com/yourname',
            self::YouTube => 'https://www.youtube.com/@yourname',
            self::TikTok => 'https://www.tiktok.com/@yourname',
            self::Pinterest => 'https://www.pinterest.com/yourname/',
            self::Threads => 'https://www.threads.com/@yourname',
            self::Bluesky => 'https://bsky.app/profile/yourname.bsky.social',
            self::Mastodon => 'https://mastodon.social/@yourname',
            self::Vimeo => 'https://vimeo.com/yourname',
            self::Behance => 'https://www.behance.net/yourname',
        };
    }

    /**
     * How an editor may give the account instead of its address, or null
     * when only the address will do.
     */
    public function handleExample(): ?string
    {
        return match ($this) {
            self::LinkedIn => null,
            self::Bluesky => 'yourname.bsky.social',
            self::Mastodon => '@yourname@mastodon.social',
            default => '@yourname',
        };
    }

    /**
     * The network's mark.
     */
    public function icon(): Icon
    {
        return Icon::from($this->value);
    }

    /**
     * The network's name, as the network writes it.
     */
    public function label(): string
    {
        return match ($this) {
            self::Facebook => 'Facebook',
            self::Instagram => 'Instagram',
            self::LinkedIn => 'LinkedIn',
            self::X => 'X',
            self::YouTube => 'YouTube',
            self::TikTok => 'TikTok',
            self::Pinterest => 'Pinterest',
            self::Threads => 'Threads',
            self::Bluesky => 'Bluesky',
            self::Mastodon => 'Mastodon',
            self::Vimeo => 'Vimeo',
            self::Behance => 'Behance',
        };
    }

    /**
     * Where a site's settings keep the profile, as a dotted key. The five
     * networks the CMS's Social Media Links plugin had keep its keys; X is
     * still social_twitter.
     */
    public function settingKey(): string
    {
        return 'social.social_' . (self::X === $this ? 'twitter' : $this->value);
    }
}
