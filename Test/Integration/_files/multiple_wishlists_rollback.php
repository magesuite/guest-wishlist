<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$sharingCodes = [
    'guest_wishlist',
    'guest_wishlist_expired',
    'empty_guest_wishlist',
    'customer_wishlist',
    'empty_customer_wishlist'
];

foreach ($sharingCodes as $sharingCode) {
    $wishlist = $objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
    $wishlist->load($sharingCode, 'sharing_code');

    if ($wishlist->getId()) {
        $wishlist->delete();
    }
}
