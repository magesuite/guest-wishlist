<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Helper\Data;

class CountItemsForGuestWishlist
{
    public function __construct(
        protected \Magento\Customer\Model\Session $customerSession,
        protected \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider,
        protected \MageSuite\GuestWishlist\Helper\Configuration $configuration
    ) {
    }

    public function aroundGetItemCount(\Magento\Wishlist\Helper\Data $subject, callable $proceed)
    {
        if (!$this->isCustomerGuest()) {
            return $proceed();
        }

        return $this->countWishlistItems();
    }

    protected function countWishlistItems(): int|float
    {
        $wishlist = $this->cookieBasedWishlistProvider->getWishlist(false);
        if (!$wishlist || !$wishlist->getId()) {
            return 0;
        }

        $collection = $wishlist->getItemCollection()->setInStockFilter(true);

        return $this->configuration->getUseQtyInWishlist()
            ? $collection->getItemsQty()
            : $collection->count();
    }

    public function isCustomerGuest(): bool
    {
        return !$this->customerSession->isLoggedIn();
    }
}
