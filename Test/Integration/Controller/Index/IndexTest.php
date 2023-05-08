<?php
declare(strict_types=1);
namespace MageSuite\GuestWishlist\Test\Integration\Controller\Index;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoAppArea frontend
     */
    public function testItIsPossibleToReachWishlistAsGuest()
    {
        $this->dispatch('wishlist/index/index');
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }
}
