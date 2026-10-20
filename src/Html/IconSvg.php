<?php

declare(strict_types=1);

namespace Contenir\Social\Html;

use Contenir\Social\Icon\Icon;

use function array_map;
use function implode;
use function sprintf;

/**
 * An icon as inline SVG: filled with the text colour, 24 by 24 until a
 * site's CSS sizes it, and hidden from assistive technology, since the link
 * or button it sits in carries the name.
 *
 * @api
 */
final class IconSvg
{
    public static function render(Icon $icon, string $class = 'social__icon'): string
    {
        return sprintf(
            '<svg class="%s" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true"'
                . ' focusable="false">%s</svg>',
            Escape::html($class),
            implode('', array_map(
                static fn(string $path): string => sprintf('<path d="%s"/>', Escape::html($path)),
                $icon->paths(),
            )),
        );
    }
}
