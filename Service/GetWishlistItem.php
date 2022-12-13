<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Service;

class GetWishlistItem
{
    protected \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionFactory;

    public function __construct(\Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(int $wishlistId, int $productId): ?\Magento\Wishlist\Model\Item
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('product_id', ['eq' => $productId])
            ->addFieldToFilter('wishlist_id', ['eq' => $wishlistId]);
        $collection->getSelect()
            ->limit(1);

        return $collection->getFirstItem();
    }
}
