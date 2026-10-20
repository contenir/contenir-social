<?php

declare(strict_types=1);

namespace Contenir\Social\Share;

use Contenir\Social\Exception\InvalidPageException;

use function in_array;
use function is_string;
use function parse_url;
use function strtolower;
use function trim;

use const PHP_URL_HOST;
use const PHP_URL_SCHEME;

/**
 * The page being shared, from its own metadata: its canonical address,
 * title, description and share image.
 *
 * @api
 */
final readonly class Page
{
    public string $url;

    public string $title;

    public string $description;

    public ?string $image;

    /**
     * @throws InvalidPageException When $url, or $image when given, isn't an absolute http or https address.
     */
    public function __construct(
        string $url,
        string $title = '',
        string $description = '',
        ?string $image = null,
    ) {
        $this->url         = self::absolute('address', $url);
        $this->title       = trim($title);
        $this->description = trim($description);
        $this->image       = null === $image || '' === trim($image) ? null : self::absolute('image address', $image);
    }

    /**
     * @throws InvalidPageException When $url isn't an absolute http or https address.
     */
    private static function absolute(string $what, string $url): string
    {
        $url    = trim($url);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host   = parse_url($url, PHP_URL_HOST);
        if (! is_string($scheme) || ! in_array(strtolower($scheme), ['http', 'https'], strict: true)) {
            throw InvalidPageException::notAbsolute($what, $url);
        }

        if (! is_string($host)) {
            throw InvalidPageException::notAbsolute($what, $url);
        }

        return $url;
    }
}
