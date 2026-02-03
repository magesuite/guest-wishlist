<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Wishlist\Helper\Data;

class CountItemsForGuestWishlistTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Wishlist\Helper\Data $helper;
    protected ?\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->helper = $objectManager->create(\Magento\Wishlist\Helper\Data::class);
        $this->cookieBasedWishlistProvider = $objectManager->create(\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     * @magentoDataFixture MageSuite_GuestWishlist::Test/Integration/_files/guest_wishlist.php
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testItCountsItemsFromGuestWishlist(): void
    {
        $this->cookieBasedWishlistProvider->setCookieWithSharingCode('guest_wishlist');
        $itemCount = $this->helper->getItemCount();
        $this->assertEquals(1, $itemCount);
    }
}
