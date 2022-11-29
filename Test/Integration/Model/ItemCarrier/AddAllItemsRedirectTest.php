<?php

namespace MageSuite\GuestWishlist\Test\Integration\Model\ItemCarrier;

class AddAllItemsRedirectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $wishlist;

    /**
     * @var \Magento\Wishlist\Model\ItemCarrier
     */
    protected $itemCarrier;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->wishlist = $this->objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
        $this->itemCarrier = $this->objectManager->create(\Magento\Wishlist\Model\ItemCarrier::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture loadWishlist
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testItRedirectToCorrectPath()
    {
        $wishlist = $this->wishlist->loadByCode('guest_wishlist');

        $redirect = $this->itemCarrier->moveAllToCart($wishlist, null);

        $this->assertEquals(sprintf('http://localhost/index.php/wishlist/index/index/wishlist_id/%s/', $wishlist->getId()), $redirect);
    }

    public static function loadWishlist()
    {
        include __DIR__ .'/../../_files/guest_wishlist.php';
    }

    public static function loadWishlistRollback()
    {
        include __DIR__ .'/../../_files/guest_wishlist_rollback.php';
    }
}
