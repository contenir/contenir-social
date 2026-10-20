<?php

declare(strict_types=1);

namespace Contenir\Social\Exception;

use InvalidArgumentException;

use function sprintf;

/**
 * Thrown when a page to share has no absolute http or https address.
 *
 * @api
 */
final class InvalidPageException extends InvalidArgumentException implements ExceptionInterface
{
    public static function notAbsolute(string $what, string $url): self
    {
        return new self(sprintf('A page to share needs an absolute http or https %s; "%s" isn\'t one', $what, $url));
    }
}
