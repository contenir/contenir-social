<?php

declare(strict_types=1);

namespace Contenir\Social;

use Contenir\Social\Exception\InvalidProfileException;
use Contenir\Social\Internal\Location;
use Contenir\Social\Internal\ProfileRules;

use function is_string;
use function ltrim;
use function sprintf;
use function trim;

/**
 * A site's profile on one network, as its canonical https address. An
 * editor may give the address in any form the network serves it in, or
 * just the account, as yourname or with an @ before it, where the network
 * allows.
 *
 * @api
 */
final readonly class ProfileUrl
{
    /**
     * @param string $account The account, as the address names it: yourname, company/yourname, @yourname on YouTube
     */
    private function __construct(
        public Network $network,
        public string $url,
        public string $account,
    ) {}

    /**
     * Reads a profile address, or an account given on its own, as the
     * canonical address.
     *
     * @throws InvalidProfileException When $value isn't a profile on $network.
     */
    public static function parse(Network $network, string $value): self
    {
        $profile = self::read($network, trim($value));
        if (is_string($profile)) {
            throw InvalidProfileException::because($profile);
        }

        return $profile;
    }

    /**
     * What is wrong with $value as a profile on $network, for an editor;
     * null when it is one. An empty value is no profile, which is fine.
     */
    public static function problem(Network $network, string $value): ?string
    {
        $value = trim($value);
        if ('' === $value) {
            return null;
        }

        $profile = self::read($network, $value);

        return is_string($profile) ? $profile : null;
    }

    /**
     * The profile, or null when $value isn't one on $network.
     */
    public static function tryParse(Network $network, string $value): ?self
    {
        $profile = self::read($network, trim($value));

        return is_string($profile) ? null : $profile;
    }

    /**
     * The profile $value names, or what is wrong with it.
     */
    private static function read(Network $network, string $value): self|string
    {
        if ('' === $value) {
            return sprintf('Enter the %s address.', $network->label());
        }

        $location = Location::of($network, $value);
        if (null === $location) {
            return self::unrecognised($network);
        }

        $host = $location->host;
        if ('' !== $host && ! ProfileRules::serves($network, $host)) {
            return sprintf(
                'That address is on %s, not %s. Enter the profile\'s address, such as %s.',
                $host,
                $network->label(),
                $network->example(),
            );
        }

        $account = Network::Facebook === $network ? $location->facebookId() : null;
        $account ??= ProfileRules::account($network, $location->path);
        if (null === $account) {
            return self::unrecognised($network);
        }

        return new self($network, ProfileRules::address($network, $host, $account), $account);
    }

    private static function unrecognised(Network $network): string
    {
        $handle = $network->handleExample();

        return sprintf(
            'That isn\'t a profile on %s. Enter its address, such as %s%s.',
            $network->label(),
            $network->example(),
            null === $handle ? '' : ", or {$handle}",
        );
    }

    /**
     * The account, with the @ the network writes before it: @yourname. X
     * uses this for twitter:site.
     */
    public function mention(): string
    {
        return '@' . ltrim($this->account, characters: '@');
    }
}
