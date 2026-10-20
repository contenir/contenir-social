<?php

declare(strict_types=1);

namespace Contenir\Social;

/**
 * Where a site's settings keep its profiles and share buttons, as dotted
 * keys. Profiles keep the keys the CMS's Social Media Links plugin used, and
 * new networks join them under social, so anything reading profiles (the SEO
 * add-on's sameAs, a site's own templates) finds them in one place. Share
 * buttons sit under extensions.social, the add-on's own namespace.
 *
 * @api
 */
final class SettingKeys
{
    public const string FACEBOOK = 'social.social_facebook';

    public const string INSTAGRAM = 'social.social_instagram';

    public const string LINKEDIN = 'social.social_linkedin';

    /**
     * X keeps Twitter's key.
     */
    public const string X = 'social.social_twitter';

    public const string YOUTUBE = 'social.social_youtube';

    public const string TIKTOK = 'social.social_tiktok';

    public const string PINTEREST = 'social.social_pinterest';

    public const string THREADS = 'social.social_threads';

    public const string BLUESKY = 'social.social_bluesky';

    public const string MASTODON = 'social.social_mastodon';

    /**
     * The share buttons, in order.
     */
    public const string SHARE_NETWORKS = 'extensions.social.share_networks';

    /**
     * Whether share buttons show their names beside their icons.
     */
    public const string SHARE_LABELS = 'extensions.social.share_labels';

    /**
     * Whether shared addresses carry UTM tags.
     */
    public const string SHARE_UTM = 'extensions.social.share_utm';
}
