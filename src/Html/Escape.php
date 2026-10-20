<?php

declare(strict_types=1);

namespace Contenir\Social\Html;

use function htmlspecialchars;

use const ENT_HTML5;
use const ENT_QUOTES;
use const ENT_SUBSTITUTE;

/**
 * Text and attribute values made safe for HTML.
 *
 * @internal
 */
final class Escape
{
    public static function html(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, encoding: 'UTF-8');
    }
}
