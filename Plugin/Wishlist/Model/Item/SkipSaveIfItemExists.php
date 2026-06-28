<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Model\Item;

class SkipSaveIfItemExists
{
    protected array $sharedStoreIds = [];

    public function __construct(
        protected \MageSuite\GuestWishlist\Service\GetWishlistItems $getWishlistItems,
        protected \Magento\Store\Model\StoreManagerInterface $storeManager,
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
        $sharedStoreIds = $this->getSharedStoreIds((int) $subject->getStoreId());
        $subjectId = (int) $subject->getId();

        foreach ($items as $item) {
            if (!$item instanceof \Magento\Wishlist\Model\Item || !$item->getId()) {
                continue;
            }

            if ((int) $item->getId() === $subjectId) {
                continue;
            }

            $originValue = $item->getOptionByCode('simple_product')?->getValue();

            if ($guestValue === $originValue && in_array($item->getStoreId(), $sharedStoreIds)) {
                return $item;
            }
        }

        return $proceed();
    }

    protected function getSharedStoreIds(int $storeId): array
    {
        if (isset($this->sharedStoreIds[$storeId])) {
            return $this->sharedStoreIds[$storeId];
        }
        $this->sharedStoreIds[$storeId] = [];

        try {
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore($storeId);
            $storeIds = $store->getWebsite()->getStoreIds();
            $this->sharedStoreIds[$storeId] = $storeIds;

            foreach ($storeIds as $entityId) {
                $this->sharedStoreIds[(int) $entityId] = $storeIds;
            }
        } catch (\Exception $e) {
            return [];
        }

        return $this->sharedStoreIds[$storeId];
    }
}
