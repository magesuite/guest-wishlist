<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Plugin\Model\Item;

/**
 * @magentoDbIsolation disabled
 * @magentoAppArea frontend
 */
class SkipSaveIfItemExistsTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected const REFERER_URL = 'http://localhost/test';
    protected const URI = 'wishlist/index/add';

    protected ?\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager;
    protected ?\Magento\Customer\Model\Session $customerSession;
    protected ?\Magento\TestFramework\Wishlist\Model\GetWishlistByCustomerId $getWishlistByCustomerId;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\GuestWishlist\Controller\WishlistProvider $wishlistDataProvider;
    protected ?\Magento\Wishlist\Model\WishlistFactory $wishlistFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        $this->getWishlistByCustomerId = $this->_objectManager->get(\Magento\TestFramework\Wishlist\Model\GetWishlistByCustomerId::class);
        $this->productRepository = $this->_objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productRepository->cleanCache();
        $this->wishlistDataProvider = $this->_objectManager->get(\MageSuite\GuestWishlist\Controller\WishlistProvider::class);
        $this->wishlistFactory = $this->_objectManager->get(\Magento\Wishlist\Model\WishlistFactory::class);
    }

    protected function tearDown(): void
    {
        $this->customerSession->setCustomerId(null);

        parent::tearDown();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testAddItemToWishList()
    {
        $product = $this->productRepository->get('simple1');

        $this->prepareReferer();
        $this->customerSession->setCustomerId(1);

        $this->performAddToWishListRequest(['product' => $product->getId()]);

        $wishlist = $this->getCustomerWishlist(1);
        $this->assertCount(1, $wishlist->getItemCollection()->load());

        $this->customerSession->setCustomerId(null);
        $this->wishlistDataProvider->clearCache();

        $this->performAddToWishListRequest(['product' => $product->getId()]);
        $guestWishlist = $this->getCustomerWishlist(0);
        $this->assertGreaterThan($wishlist->getId(), $guestWishlist->getId());
        $this->assertCount(1, $guestWishlist->getItemCollection()->load());

        $this->login(1);

        $clientWishlist = $this->getCustomerWishlist(1);
        $this->assertEquals($wishlist->getId(), $clientWishlist->getId());
        $this->assertCount(1, $clientWishlist->getItemCollection()->load());
    }

    protected function login($customerId): void
    {
        /** @var \Magento\Customer\Model\Session $session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(\Magento\Customer\Model\Session::class);
        $session->loginById($customerId);
    }

    protected function performAddToWishListRequest(array $params): void
    {
        $this->getRequest()->setParams($params)->setMethod(\Magento\Framework\App\Request\Http::METHOD_POST);
        $this->dispatch(self::URI);
    }

    protected function prepareReferer(): void
    {
        $parameters = $this->_objectManager->create(\Laminas\Stdlib\Parameters::class);
        $parameters->set('HTTP_REFERER', self::REFERER_URL);
        $this->getRequest()->setServer($parameters);
    }

    protected function getCustomerWishlist(int $customerId): \Magento\Wishlist\Model\Wishlist
    {
        if ($customerId > 0) {
            $wishlist = $this->getWishlistByCustomerId->execute($customerId);
        } else {
            $wishlist = $this->wishlistFactory->create();
            $wishlist->load($this->cookieManager->getCookie('wishlist'), 'sharing_code');
        }

        return $wishlist;
    }
}
