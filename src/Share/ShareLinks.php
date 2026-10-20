<?php

declare(strict_types=1);

namespace Contenir\Social\Share;

use function array_filter;
use function http_build_query;
use function str_contains;
use function strpos;
use function substr;
use function trim;

use const PHP_QUERY_RFC3986;

/**
 * Builds a page's share buttons as plain links to each network. Nothing
 * reaches a network until a visitor clicks: no scripts, counts or cookies.
 *
 * @api
 */
final readonly class ShareLinks
{
    public function __construct(
        private ShareConfig $config = new ShareConfig(),
    ) {}

    /**
     * The page's address as shared to $target: with UTM tags when the site
     * asks for them, before any fragment.
     */
    public static function tag(string $url, ShareTarget $target): string
    {
        $hash     = strpos($url, needle: '#');
        $fragment = false === $hash ? '' : substr($url, $hash);
        $base     = false === $hash ? $url : substr($url, offset: 0, length: $hash);
        $query    = self::query([
            'utm_source'   => $target->value,
            'utm_medium'   => ShareTarget::Email === $target ? 'email' : 'social',
            'utm_campaign' => 'share',
        ]);

        return $base . (str_contains($base, '?') ? '&' : '?') . $query . $fragment;
    }

    /**
     * The network's share address for $url.
     */
    private static function href(ShareTarget $target, string $url, Page $page): string
    {
        $title = $page->title;
        $text  = trim("{$title} {$url}");

        return match ($target) {
            ShareTarget::Facebook => self::link('https://www.facebook.com/sharer/sharer.php', ['u' => $url]),
            ShareTarget::X => self::link('https://x.com/intent/tweet', ['text' => $title, 'url' => $url]),
            ShareTarget::LinkedIn => self::link('https://www.linkedin.com/sharing/share-offsite/', ['url' => $url]),
            ShareTarget::WhatsApp                     => self::link('https://wa.me/', ['text' => $text]),
            ShareTarget::Email => 'mailto:?'
                . self::query([
                    'subject' => $title,
                    'body'    => trim("{$page->description}\n\n{$url}"),
                ]),
            ShareTarget::Bluesky => self::link('https://bsky.app/intent/compose', ['text' => $text]),
            ShareTarget::Threads => self::link('https://www.threads.com/intent/post', [
                'text' => $title,
                'url'  => $url,
            ]),
            ShareTarget::Reddit => self::link('https://www.reddit.com/submit', ['url' => $url, 'title' => $title]),
            ShareTarget::Pinterest => self::link('https://www.pinterest.com/pin/create/button/', [
                'url'         => $url,
                'media'       => (string) $page->image,
                'description' => $title,
            ]),
            ShareTarget::Telegram => self::link('https://t.me/share/url', ['url' => $url, 'text' => $title]),
            ShareTarget::CopyLink, ShareTarget::Share => $url,
        };
    }

    /**
     * @param array<string, string> $parameters Empty ones are left out
     */
    private static function link(string $base, array $parameters): string
    {
        return $base . '?' . self::query($parameters);
    }

    /**
     * @param array<string, string> $parameters Empty ones are left out
     */
    private static function query(array $parameters): string
    {
        return http_build_query(
            array_filter($parameters, static fn(string $value): bool => '' !== $value),
            numeric_prefix: '',
            arg_separator: '&',
            encoding_type: PHP_QUERY_RFC3986,
        );
    }

    /**
     * The page's share buttons, in the site's order. Pinterest is left out
     * when the page has no image to pin.
     *
     * @return list<ShareLink>
     */
    public function for(Page $page): array
    {
        $links = [];
        foreach ($this->config->targets as $target) {
            if ($target->needsImage() && null === $page->image) {
                continue;
            }

            $url     = $this->config->utm ? self::tag($page->url, $target) : $page->url;
            $links[] = new ShareLink($target, self::href($target, $url, $page), $url, $page->title);
        }

        return $links;
    }
}
