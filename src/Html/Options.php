<?php

declare(strict_types=1);

namespace Contenir\Social\Html;

/**
 * How profiles and share buttons are written: with icons, names or both,
 * whether links open a new tab, and the BEM block their classes are named
 * after (social, social__item, social__link…), so a site can use its own.
 *
 * @api
 */
final readonly class Options
{
    /**
     * @param bool    $icons  Draw each network's icon
     * @param ?bool   $labels Show each name beside its icon; null leaves it to the default (profiles: no; share: the site's setting)
     * @param bool    $newTab Open profiles and networks in a new tab, and say so in each link's name
     * @param string  $block  The BEM block the classes are named after
     * @param string  $class  More classes for the list
     */
    public function __construct(
        public bool $icons = true,
        public ?bool $labels = null,
        public bool $newTab = true,
        public string $block = 'social',
        public string $class = '',
    ) {}

    /**
     * Whether names show, given the default for what is being written.
     * Without icons, they always do.
     */
    public function showsLabels(bool $default): bool
    {
        if (! $this->icons) {
            return true;
        }

        return $this->labels ?? $default;
    }
}
