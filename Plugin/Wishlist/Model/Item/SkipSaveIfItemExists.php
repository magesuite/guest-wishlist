<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Model\Item;

class SkipSaveIfItemExists
{
    public function __construct(
        protected \MageSuite\GuestWishlist\Service\GetExistingWishlistItem $getExistingWishlistItem
    ) {
    }

    public function aroundSave(
        \Magento\Wishlist\Model\Item $subject,
        callable $proceed
    ): \Magento\Wishlist\Model\Item {
        return $this->getExistingWishlistItem->execute($subject) ?? $proceed();
    }
}
