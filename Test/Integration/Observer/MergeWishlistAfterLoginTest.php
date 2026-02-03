<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Observer;

class MergeWishlistAfterLoginTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager;
    protected ?\Magento\Customer\Model\Session $customerSession;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     * @magentoDataFixture MageSuite_GuestWishlist::Test/Integration/_files/guest_wishlist.php
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testGuestWishlistItemsAreAssignedToCustomerAfterLoggingIn(): void
    {
        $wishlist = $this->_objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
        $wishlist->loadByCustomerId(1);
        $this->assertEquals(0, $wishlist->getItemsCount());

        $this->getRequest()
            ->setMethod('POST')
            ->setPostValue(
                [
                    'login' => [
                        'username' => 'customer@example.com',
                        'password' => '123123q'
                    ]
                ]
            );

        $this->cookieManager->setPublicCookie(
            'wishlist',
            'guest_wishlist'
        );

        $this->dispatch('customer/account/loginPost');

        $wishlist = $this->_objectManager->create(\Magento\Wishlist\Model\Wishlist::class);
        $wishlist->loadByCustomerId(1);

        $this->assertEquals(1, $wishlist->getItemsCount());
    }

    protected function tearDown(): void
    {
        $this->customerSession->logout();
    }
}
