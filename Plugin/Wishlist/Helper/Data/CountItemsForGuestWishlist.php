<?php

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Helper\Data;

/**
 * Default logic for counting wishlist items to display them in badge uses customer session
 * This plugin provides logic that counts wishlist items when customer is not logged in
 */
class CountItemsForGuestWishlist
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    /**
     * @var \MageSuite\GuestWishlist\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider,
        \MageSuite\GuestWishlist\Helper\Configuration $configuration
    )
    {
        $this->customerSession = $customerSession;
        $this->wishlistFactory = $wishlistFactory;
        $this->cookieBasedWishlistProvider = $cookieBasedWishlistProvider;
        $this->configuration = $configuration;
    }

    public function aroundGetItemCount(\Magento\Wishlist\Helper\Data $subject, callable $proceed)
    {
        if (!$this->isCustomerGuest()) {
            return $proceed();
        }

        return $this->countWishlistItems();
    }

    protected function countWishlistItems()
    {
        $customerId = $this->customerSession->getCustomerId();

        if (!$customerId) {
            $guestWishlist = $this->cookieBasedWishlistProvider->getWishlist(true);
        } else {
            $guestWishlist = $this->wishlistFactory->create();
            $guestWishlist->loadByCustomerId($customerId, true);
        }

        $collection = $guestWishlist
            ->getItemCollection()
            ->setInStockFilter(true);

        $useQty = $this->configuration->getUseQtyInWishlist();

        return $useQty ? $collection->getItemsQty() : $collection->count();
    }

    /**
     * @return bool
     */
    public function isCustomerGuest()
    {
        return !$this->customerSession->isLoggedIn();
    }
}
