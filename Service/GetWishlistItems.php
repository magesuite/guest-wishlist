<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Service;

class GetWishlistItems
{
    protected \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionFactory;

    public function __construct(\Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Wishlist\Model\Item[]
     */
    public function execute(int $wishlistId, int $productId): array
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('product_id', ['eq' => $productId])
            ->addFieldToFilter('wishlist_id', ['eq' => $wishlistId]);

        return $collection->getItems();
    }
}
