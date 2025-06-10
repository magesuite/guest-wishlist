<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Model\Item;

class SkipSaveIfItemExists
{
    public function __construct(
        protected \MageSuite\GuestWishlist\Service\GetWishlistItems $getWishlistItems,
    ) {
    }

    public function aroundSave(
        \Magento\Wishlist\Model\Item $subject,
        callable $proceed
    ): \Magento\Wishlist\Model\Item {
        if ($subject->isDeleted()) {
            return $proceed();
        }
        $items = $this->getWishlistItems->execute((int) $subject->getWishlistId(), (int) $subject->getProductId());
        $guestValue = $subject->getOptionByCode('simple_product')?->getValue();

        foreach ($items as $item) {
            if ($item instanceof \Magento\Wishlist\Model\Item && $item->getId()) {
                $originValue = $item->getOptionByCode('simple_product')?->getValue();

                if ($guestValue === $originValue) {
                    return $item;
                }
            }
        }

        return $proceed();
    }
}
