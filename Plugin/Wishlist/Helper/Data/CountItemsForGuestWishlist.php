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
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->customerSession = $customerSession;
        $this->cookieBasedWishlistProvider = $cookieBasedWishlistProvider;
        $this->scopeConfig = $scopeConfig;
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
        $guestWishlist = $this->cookieBasedWishlistProvider->getWishlist(true);

        $collection = $guestWishlist
            ->getItemCollection()
            ->setInStockFilter(true);

        $useQty = $this->scopeConfig->getValue(
            \Magento\Wishlist\Helper\Data::XML_PATH_WISHLIST_LINK_USE_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

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
