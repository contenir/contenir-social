<?php

declare(strict_types=1);

namespace Contenir\Social\Share;

/**
 * One share button: where it goes, or for "Copy link" and "Share…" the
 * address the share script copies or hands to the browser.
 *
 * @api
 */
final readonly class ShareLink
{
    /**
     * @param string $href  The network's share address; the page's own address for a button
     * @param string $url   The page's address as shared, with any UTM tags
     * @param string $title The page's title, which "Share…" hands to the browser
     */
    public function __construct(
        public ShareTarget $target,
        public string $href,
        public string $url,
        public string $title,
    ) {}

    /**
     * Whether it is a button for the share script rather than a link.
     */
    public function isButton(): bool
    {
        return $this->target->isButton();
    }

    /**
     * The button's text, or the network's name.
     */
    public function label(): string
    {
        return $this->target->label();
    }
}
