<?php

namespace MageSuite\GuestWishlist\Service;

class WishlistMerger
{
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    public function __construct(\Magento\Wishlist\Model\WishlistFactory $wishlistFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * @param $guestWishlist \Magento\Wishlist\Model\Wishlist
     * @param $customer \Magento\Customer\Model\Customer
     */
    public function assignGuestWishlistItemsToCustomer($guestWishlist, $customer) {
        $customerId = $customer->getId();
        $customerWishlist = $this->wishlistFactory->create();
        $customerWishlist->loadByCustomerId($customerId, true);

        $guestWishlistItems = $guestWishlist->getItemCollection();

        /** @var \Magento\Wishlist\Model\Item $item */
        foreach($guestWishlistItems as $item) {
            $item->setWishlistId($customerWishlist->getId());
            $item->save();
        }
    }
}