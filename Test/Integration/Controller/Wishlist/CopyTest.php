<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Controller\Wishlist;

class CopyTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\Message\ManagerInterface $messages;
    protected ?\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messages = $this->_objectManager->get(\Magento\Framework\Message\ManagerInterface::class);
        $this->cookieBasedWishlistProvider = $this->_objectManager->get(\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_GuestWishlist::Test/Integration/_files/guest_wishlist.php
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testWishlistIsSharedAfterUsingCopyLink(): void
    {
        $originalWishlist = $this->cookieBasedWishlistProvider->getWishlist(true);
        $this->assertEquals(0, $originalWishlist->getItemsCount());

        $this->dispatch('guest_wishlist/wishlist/copy/sharing_code/guest_wishlist');

        $assignedWishlist = $this->cookieBasedWishlistProvider->getWishlist(true);
        $this->assertEquals(1, $assignedWishlist->getItemsCount());
    }
}
