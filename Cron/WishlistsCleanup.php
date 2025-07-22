<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Cron;

class WishlistsCleanup
{
    public function __construct(
        protected \MageSuite\GuestWishlist\Model\ResourceModel\Wishlist $resourceModel,
        protected \MageSuite\GuestWishlist\Helper\Configuration $configuration
    ) {
    }

    public function execute(): void
    {
        $this->cleanupEmptyWishlists();
        $this->cleanupExpiredWishlists();
    }

    protected function cleanupEmptyWishlists(): void
    {
        $this->removeWishlists((int)$this->configuration->getEmptyWishlistsRetentionPeriod(), true);
    }

    protected function cleanupExpiredWishlists(): void
    {
        $this->removeWishlists((int)$this->configuration->getGuestWishlistCookieLifetimeInDays(), false);
    }

    protected function removeWishlists(int $retentionPeriodInDays, bool $emptyOnly): void
    {
        if ($retentionPeriodInDays <= 0) {
            return;
        }

        $this->resourceModel->removeOlderThan($retentionPeriodInDays, $emptyOnly);
    }
}
