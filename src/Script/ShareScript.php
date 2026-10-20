<?php

declare(strict_types=1);

namespace Contenir\Social\Script;

use Contenir\Social\Html\Escape;

use function file_get_contents;
use function sprintf;

/**
 * The script behind "Copy link" and "Share…": no dependencies, nothing
 * sent anywhere, and safe to include more than once. Without it, both
 * buttons stay hidden and the share links still work. The source never
 * contains "</", so it can be written inline as it is.
 *
 * @api
 */
final class ShareScript
{
    /**
     * Where the script's source is.
     */
    public const string PATH = __DIR__ . '/../../resources/share.js';

    /**
     * The script's source.
     */
    public static function source(): string
    {
        return (string) file_get_contents(self::PATH);
    }

    /**
     * The script as an inline script element, with the page's CSP nonce
     * when it has one.
     */
    public static function tag(?string $nonce = null): string
    {
        return sprintf(
            '<script%s>%s</script>',
            null === $nonce || '' === $nonce ? '' : sprintf(' nonce="%s"', Escape::html($nonce)),
            self::source(),
        );
    }
}
