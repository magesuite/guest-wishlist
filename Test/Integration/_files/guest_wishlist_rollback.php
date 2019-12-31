<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$wishlist = $objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
$wishlist->load('guest_wishlist', 'sharing_code');

if ($wishlist->getId()) {
    $wishlist->delete();
}
