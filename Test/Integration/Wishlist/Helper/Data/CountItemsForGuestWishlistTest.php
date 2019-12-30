<?php

namespace MageSuite\GuestWishlist\Test\Integration\Wishlist\Helper\Data;

class CountItemsForGuestWishlistTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $helper;

    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->helper = $this->objectManager->create(\Magento\Wishlist\Helper\Data::class);
        $this->cookieBasedWishlistProvider = $this->objectManager->create(\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     * @magentoDataFixture loadWishlist
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testItCountsItemsFromGuestWishlist()
    {
        $this->cookieBasedWishlistProvider->setCookieWithSharingCode('guest_wishlist');

        $itemCount = $this->helper->getItemCount();
        $this->assertEquals(1, $itemCount);
    }

    public static function loadWishlist()
    {
        include __DIR__ .'/../../../_files/guest_wishlist.php';
    }

    public static function loadWishlistRollback()
    {
        include __DIR__ .'/../../../_files/guest_wishlist_rollback.php';
    }
}
