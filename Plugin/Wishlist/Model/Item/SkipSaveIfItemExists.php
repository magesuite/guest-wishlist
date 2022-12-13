<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Model\Item;

class SkipSaveIfItemExists
{
    protected \MageSuite\GuestWishlist\Service\GetWishlistItem $getWishlistItem;

    public function __construct(
        \MageSuite\GuestWishlist\Service\GetWishlistItem $getWishlistItem
    ) {
        $this->getWishlistItem = $getWishlistItem;
    }

    public function aroundSave(
        \Magento\Wishlist\Model\Item $subject,
        callable $proceed
    ): \Magento\Wishlist\Model\Item {

        $item = $this->getWishlistItem->execute((int) $subject->getWishlistId(), (int) $subject->getProductId());

        if ($item instanceof \Magento\Wishlist\Model\Item && $item->getId()) {
            return $item;
        }

        return $proceed();
    }
}
