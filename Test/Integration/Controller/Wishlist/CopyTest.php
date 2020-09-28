<?php

namespace MageSuite\GuestWishlist\Test\Integration\Controller\Wishlist;

class CopyTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messages;

    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messages = $this->_objectManager->get(\Magento\Framework\Message\ManagerInterface::class);
        $this->cookieBasedWishlistProvider = $this->_objectManager->get(\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture loadWishlist
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testWishlistIsSharedAfterUsingCopyLink()
    {
        $originalWishlist = $this->cookieBasedWishlistProvider->getWishlist(true);
        $this->assertEquals(0, $originalWishlist->getItemsCount());

        $this->dispatch('guest_wishlist/wishlist/copy/sharing_code/guest_wishlist');

        $assignedWishlist = $this->cookieBasedWishlistProvider->getWishlist(true);
        $this->assertEquals(1, $assignedWishlist->getItemsCount());
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
