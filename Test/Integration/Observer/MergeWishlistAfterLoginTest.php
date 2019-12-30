<?php

namespace MageSuite\GuestWishlist\Test\Integration\Observer;

class MergeWishlistAfterLoginTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected function setUp()
    {
        parent::setUp();

        $this->cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     * @magentoDataFixture loadWishlist
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testGuestWishlistItemsAreAssignedToCustmerAfterLoggingIn()
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

    protected function tearDown()
    {
        $this->customerSession->logout();
    }

    public static function loadWishlist()
    {
        include __DIR__ .'/../_files/guest_wishlist.php';
    }

    public static function loadWishlistRollback()
    {
        include __DIR__ .'/../_files/guest_wishlist_rollback.php';
    }
}
