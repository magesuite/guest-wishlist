<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$product = $productRepository->get('simple');

$wishlist = $objectManager->create(\Magento\Wishlist\Model\Wishlist::class);

$wishlist->setCustomerId(0);
$wishlist->setSharingCode('guest_wishlist');
$wishlist->save();

$item = $wishlist->addNewItem($product, new \Magento\Framework\DataObject([]));
