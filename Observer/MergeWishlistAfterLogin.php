<?php

namespace MageSuite\GuestWishlist\Observer;

class MergeWishlistAfterLogin implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    /**
     * @var \MageSuite\GuestWishlist\Test\Integration\Controller\WishlistMerger
     */
    protected $wishlistMerger;

    public function __construct(
        \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider,
        \MageSuite\GuestWishlist\Service\WishlistMerger $wishlistMerger
    ) {
        $this->cookieBasedWishlistProvider = $cookieBasedWishlistProvider;
        $this->wishlistMerger = $wishlistMerger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getData('customer');
        $guestWishlist = $this->cookieBasedWishlistProvider->getWishlist(false);

        if($guestWishlist == null) {
            return;
        }

        $this->wishlistMerger->assignGuestWishlistItemsToCustomer($guestWishlist, $customer);
    }
}