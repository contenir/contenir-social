<?php

declare(strict_types=1);

namespace Contenir\Social\Html;

use Contenir\Social\Share\ShareLink;
use Contenir\Social\Share\ShareTarget;

use function implode;
use function sprintf;
use function trim;

/**
 * A page's share buttons as a list. Networks are plain links; "Copy link"
 * and "Share…" are buttons, written hidden for the share script to show
 * where the browser can copy or has a share sheet.
 *
 *     <ul class="social social--share">
 *       <li class="social__item social__item--x"><a class="social__link" href="https://x.com/intent/tweet?…"
 *           target="_blank" rel="noopener noreferrer" aria-label="Share on X (opens in a new tab)">…</a></li>
 *       <li class="social__item social__item--copy" hidden><button class="social__button" type="button"
 *           data-social-copy="…">…</button><span class="social__status" role="status" data-social-copied="Copied"></span></li>
 *     </ul>
 *
 * @api
 */
final class ShareButtons
{
    /**
     * The list, or an empty string when there are no buttons.
     *
     * Names show beside icons unless the options say otherwise.
     *
     * @param list<ShareLink> $links
     */
    public static function render(array $links, Options $options = new Options()): string
    {
        if ([] === $links) {
            return '';
        }

        $items = [];
        foreach ($links as $link) {
            $items[] = self::item($link, $options);
        }

        return sprintf(
            '<ul class="%s">%s</ul>',
            Escape::html(trim("{$options->block} {$options->block}--share {$options->class}")),
            implode('', $items),
        );
    }

    private static function button(ShareLink $link, Options $options, string $content): string
    {
        $block = $options->block;
        $copy  = ShareTarget::CopyLink === $link->target;

        return sprintf(
            '<button class="%s" type="button" %s="%s"%s aria-label="%s">%s</button>%s',
            Escape::html("{$block}__button"),
            $copy ? 'data-social-copy' : 'data-social-share',
            Escape::html($link->url),
            $copy || '' === $link->title ? '' : sprintf(' data-social-title="%s"', Escape::html($link->title)),
            Escape::html($link->target->name()),
            $content,
            $copy
                ? sprintf(
                    '<span class="%s" role="status" data-social-copied="Copied"></span>',
                    Escape::html("{$block}__status"),
                )
                : '',
        );
    }

    private static function item(ShareLink $link, Options $options): string
    {
        $labels  = $options->showsLabels(default: true);
        $block   = $options->block;
        $target  = $link->target;
        $content = ($options->icons ? IconSvg::render($target->icon(), "{$block}__icon") : '')
        . (
            $labels
                ? sprintf('<span class="%s">%s</span>', Escape::html("{$block}__label"), Escape::html($link->label()))
                : ''
        );

        return sprintf(
            '<li class="%s"%s>%s</li>',
            Escape::html("{$block}__item {$block}__item--{$target->value}"),
            $link->isButton() ? ' hidden' : '',
            $link->isButton() ? self::button($link, $options, $content) : self::link($link, $options, $content),
        );
    }

    private static function link(ShareLink $link, Options $options, string $content): string
    {
        $target = $link->target;
        $newTab = $options->newTab && $target->opensNewTab();

        return sprintf(
            '<a class="%s" href="%s"%s rel="noopener noreferrer" aria-label="%s">%s</a>',
            Escape::html("{$options->block}__link"),
            Escape::html($link->href),
            $newTab ? ' target="_blank"' : '',
            Escape::html($newTab ? $target->newTabName() : $target->name()),
            $content,
        );
    }
}
