<?php

declare(strict_types=1);

namespace Contenir\Social\Exception;

use InvalidArgumentException;

/**
 * Thrown when a value isn't a profile on its network. The message is the
 * one an editor would see.
 *
 * @api
 */
final class InvalidProfileException extends InvalidArgumentException implements ExceptionInterface
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
