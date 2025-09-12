<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Cron;

class WishlistsCleanupTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\GuestWishlist\Cron\WishlistsCleanup $wishlistsCleanup = null;
    protected ?\Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistCollection = null;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->wishlistsCleanup = $objectManager->get(\MageSuite\GuestWishlist\Cron\WishlistsCleanup::class);
        $this->wishlistCollection = $objectManager->create(\Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_GuestWishlist::Test/Integration/_files/multiple_wishlists.php
     * @magentoDbIsolation enabled
     * @magentoAppArea adminhtml
     */
    public function testCleanupWishlists()
    {
        $expectedSharedCodesBeforeCleanup = [
            'guest_wishlist',
            'guest_wishlist_expired',
            'empty_guest_wishlist',
            'customer_wishlist',
            'empty_customer_wishlist',
        ];

        $wishlistsBeforeCleanup = $this->wishlistCollection->create();
        $actualCount = 0;

        foreach ($wishlistsBeforeCleanup as $wishlist) {
            if (in_array($wishlist->getSharingCode(), $expectedSharedCodesBeforeCleanup)) {
                $actualCount++;
            }
        }

        $this->assertEquals(count($expectedSharedCodesBeforeCleanup), $actualCount);

        $this->wishlistsCleanup->execute();

        $expectedSharedCodesAfterCleanup = [
            'guest_wishlist',
            'customer_wishlist',
            'empty_customer_wishlist',
        ];

        $wishlistsAfterCleanup = $this->wishlistCollection->create();
        $sharedCodes = [];
        $actualCount = 0;

        foreach ($wishlistsAfterCleanup as $wishlist) {
            $sharedCodes[] = $wishlist->getSharingCode();

            if (in_array($wishlist->getSharingCode(), $expectedSharedCodesAfterCleanup)) {
                $actualCount++;
            }
        }

        $this->assertEquals(count($expectedSharedCodesAfterCleanup), $actualCount);

        $this->assertNotContains('guest_wishlist_expired', $sharedCodes);
        $this->assertNotContains('empty_guest_wishlist', $sharedCodes);
    }
}
