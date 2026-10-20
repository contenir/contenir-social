<?php

declare(strict_types=1);

namespace Contenir\Social\Html;

use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;

use function implode;
use function sprintf;
use function trim;

/**
 * A site's profiles as a list of links, each with rel="me" so the networks
 * that check it (Mastodon, Bluesky's verification) see the site and profile
 * belong together. Icons alone are the default, with each network's name as
 * the link's accessible name.
 *
 *     <ul class="social social--profiles">
 *       <li class="social__item social__item--instagram">
 *         <a class="social__link" href="…" rel="me noopener" target="_blank"
 *            aria-label="Instagram (opens in a new tab)"><svg class="social__icon" …></a>
 *       </li>
 *     </ul>
 *
 * @api
 */
final class ProfileLinks
{
    /**
     * The list, or an empty string when the site has no profiles.
     */
    public static function render(Profiles $profiles, Options $options = new Options()): string
    {
        $items = [];
        foreach ($profiles->all() as $profile) {
            $items[] = self::item($profile, $options);
        }

        if ([] === $items) {
            return '';
        }

        return sprintf(
            '<ul class="%s">%s</ul>',
            Escape::html(trim("{$options->block} {$options->block}--profiles {$options->class}")),
            implode('', $items),
        );
    }

    private static function item(ProfileUrl $profile, Options $options): string
    {
        $block   = $options->block;
        $network = $profile->network;
        $label   = $network->label();
        $icon    = $options->icons ? IconSvg::render($network->icon(), "{$block}__icon") : '';
        $text    = $options->showsLabels(default: false)
            ? sprintf('<span class="%s">%s</span>', Escape::html("{$block}__label"), Escape::html($label))
            : '';
        $tab  = $options->newTab ? ' target="_blank"' : '';
        $name = $options->newTab ? "{$label} (opens in a new tab)" : $label;

        return sprintf(
            '<li class="%s"><a class="%s" href="%s" rel="me noopener"%s aria-label="%s">%s%s</a></li>',
            Escape::html("{$block}__item {$block}__item--{$network->value}"),
            Escape::html("{$block}__link"),
            Escape::html($profile->url),
            $tab,
            Escape::html($name),
            $icon,
            $text,
        );
    }
}
