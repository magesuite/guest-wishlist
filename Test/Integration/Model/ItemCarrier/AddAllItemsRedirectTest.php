<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Model\ItemCarrier;

class AddAllItemsRedirectTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Wishlist\Model\Wishlist $wishlist;
    protected ?\Magento\Wishlist\Model\ItemCarrier $itemCarrier;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->wishlist = $objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
        $this->itemCarrier = $objectManager->create(\Magento\Wishlist\Model\ItemCarrier::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_GuestWishlist::Test/Integration/_files/guest_wishlist.php
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testItRedirectToCorrectPath(): void
    {
        $wishlist = $this->wishlist->loadByCode('guest_wishlist');
        $redirect = $this->itemCarrier->moveAllToCart($wishlist, null);

        $this->assertEquals(sprintf('http://localhost/index.php/wishlist/index/index/wishlist_id/%s/', $wishlist->getId()), $redirect);
    }
}
