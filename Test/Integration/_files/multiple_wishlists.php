<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$product->isObjectNew(true);
$product->priceReindexCallback();

$wishlists = [
    ['shared_code' => 'guest_wishlist', 'customer_id' => 0, 'empty' => false],
    ['shared_code' => 'guest_wishlist_expired', 'customer_id' => 0, 'empty' => false],
    ['shared_code' => 'empty_guest_wishlist', 'customer_id' => 0, 'empty' => true],
    ['shared_code' => 'customer_wishlist', 'customer_id' => 123, 'empty' => false],
    ['shared_code' => 'empty_customer_wishlist', 'customer_id' => 123, 'empty' => true],
];

foreach ($wishlists as $wishlistData) {
    $wishlist = $objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
    $wishlist->setCustomerId($wishlistData['customer_id']);
    $wishlist->setSharingCode($wishlistData['shared_code']);
    $wishlist->save();

    if (!$wishlistData['empty']) {
        $item = $wishlist->addNewItem($product, new \Magento\Framework\DataObject([]));
    }
}

$wishlistUpdateAtData = [
    ['shared_codes' => ['empty_customer_wishlist', 'empty_guest_wishlist', 'guest_wishlist'], 'interval' => '-2 days'],
    ['shared_codes' => ['guest_wishlist_expired', 'customer_wishlist'], 'interval' => '-366 days']
];

foreach ($wishlistUpdateAtData as $data) {
    $wishlist->getResource()->getConnection()->update(
        $wishlist->getResource()->getMainTable(),
        ['updated_at' => date('Y-m-d H:i:s', strtotime($data['interval']))],
        ['sharing_code IN (?)' => $data['shared_codes']]
    );
}
