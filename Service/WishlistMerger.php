<?php

namespace MageSuite\GuestWishlist\Service;

class WishlistMerger
{
    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item
     */
    protected $itemResource;

    /**
     * @var \MageSuite\GuestWishlist\Service\GetExistingWishlistItem
     */
    protected $getExistingWishlistItem;

    /**
     * @var bool
     */
    protected $mergeDuplicates;

    public function __construct(
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\ResourceModel\Item $itemResource,
        \MageSuite\GuestWishlist\Service\GetExistingWishlistItem $getExistingWishlistItem,
        bool $mergeDuplicates = false
    ) {
        $this->mergeDuplicates = $mergeDuplicates;
        $this->wishlistFactory = $wishlistFactory;
        $this->itemResource = $itemResource;
        $this->getExistingWishlistItem = $getExistingWishlistItem;
    }

    /**
     * @param $guestWishlist \Magento\Wishlist\Model\Wishlist
     * @param $customer \Magento\Customer\Model\Customer
     */
    public function assignGuestWishlistItemsToCustomer($guestWishlist, $customer)
    {
        $customerId = $customer->getId();
        $customerWishlist = $this->wishlistFactory->create();
        $customerWishlist->loadByCustomerId($customerId, true);

        $guestWishlistItems = $guestWishlist->getItemCollection();

        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($guestWishlistItems as $item) {
            $item->setWishlistId($customerWishlist->getId());
            $existingItem = $this->mergeDuplicates ? $this->getExistingWishlistItem->execute($item) : null;

            if ($existingItem !== null && $existingItem->getId() != $item->getId()) {
                $this->itemResource->delete($item);
            } else {
                $this->itemResource->save($item);
            }
        }
    }
}
