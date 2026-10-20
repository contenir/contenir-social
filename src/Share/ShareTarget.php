<?php

declare(strict_types=1);

namespace Contenir\Social\Share;

use Contenir\Social\Icon\Icon;

/**
 * Somewhere a visitor can share a page, in the order sites offer them. Each
 * is a plain link to the network, except "Copy link" and "Share…", which are
 * buttons the share script switches on.
 *
 * @api
 */
enum ShareTarget: string
{
    case Facebook  = 'facebook';
    case X         = 'x';
    case LinkedIn  = 'linkedin';
    case WhatsApp  = 'whatsapp';
    case Email     = 'email';
    case Bluesky   = 'bluesky';
    case Threads   = 'threads';
    case Reddit    = 'reddit';
    case Pinterest = 'pinterest';
    case Telegram  = 'telegram';
    case CopyLink  = 'copy';
    case Share     = 'share';

    /**
     * The button's icon: the network's mark, or an envelope, a link or a
     * paper plane.
     */
    public function icon(): Icon
    {
        return match ($this) {
            self::CopyLink => Icon::Link,
            default => Icon::from($this->value),
        };
    }

    /**
     * Whether it is a button for the share script rather than a link.
     */
    public function isButton(): bool
    {
        return self::CopyLink === $this || self::Share === $this;
    }

    /**
     * Its name, as the network writes it, or the button's text.
     */
    public function label(): string
    {
        return match ($this) {
            self::Facebook => 'Facebook',
            self::X => 'X',
            self::LinkedIn => 'LinkedIn',
            self::WhatsApp => 'WhatsApp',
            self::Email => 'Email',
            self::Bluesky => 'Bluesky',
            self::Threads => 'Threads',
            self::Reddit => 'Reddit',
            self::Pinterest => 'Pinterest',
            self::Telegram => 'Telegram',
            self::CopyLink => 'Copy link',
            self::Share => 'Share…',
        };
    }

    /**
     * The button's accessible name: what it does.
     */
    public function name(): string
    {
        return match ($this) {
            self::Email => 'Share by email',
            self::CopyLink, self::Share => $this->label(),
            default => "Share on {$this->label()}",
        };
    }

    /**
     * Whether a page needs an image to be shared there. Pinterest pins an
     * image, so it is left out for pages without one.
     */
    public function needsImage(): bool
    {
        return self::Pinterest === $this;
    }

    /**
     * The accessible name when links to networks open a new tab, which it
     * says.
     */
    public function newTabName(): string
    {
        return $this->opensNewTab() ? "{$this->name()} (opens in a new tab)" : $this->name();
    }

    /**
     * Whether the button opens the network in a new tab.
     */
    public function opensNewTab(): bool
    {
        return ! $this->isButton() && self::Email !== $this;
    }
}
