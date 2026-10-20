<?php

declare(strict_types=1);

namespace Contenir\Social\Tests\Unit\Internal;

use Contenir\Social\Internal\Account;
use Contenir\Social\Network;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class AccountTest extends TestCase
{
    #[Test]
    public function needsAMastodonUser(): void
    {
        static::assertNull(Account::locate(Network::Mastodon, '@aus.social'));
    }

    #[Test]
    public function needsTheWholeAddressForLinkedIn(): void
    {
        static::assertNull(Account::locate(Network::LinkedIn, 'houses'));
    }

    #[Test]
    public function pointsAMastodonAccountAtItsServer(): void
    {
        $location = Account::locate(Network::Mastodon, 'houses@Aus.Social');

        static::assertSame(['aus.social', '/@houses'], [$location?->host, $location?->path]);
    }

    #[Test]
    public function pointsOtherAccountsAtTheNetworksOwnHost(): void
    {
        $location = Account::locate(Network::TikTok, 'houses');

        static::assertSame(['', '/@houses'], [$location?->host, $location?->path]);
    }
}
