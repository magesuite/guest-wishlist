<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Test\Integration\Controller;

class UpdateItemOptionsTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\Data\Form\FormKey $formKey;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager;
    protected ?\Magento\Wishlist\Model\WishlistFactory $wishlistFactory;
    protected ?\MageSuite\GuestWishlist\Controller\WishlistProvider $wishlistProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
        $this->productRepository = $this->_objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->wishlistFactory = $this->_objectManager->get(\Magento\Wishlist\Model\WishlistFactory::class);
        $this->wishlistProvider = $this->_objectManager->get(\MageSuite\GuestWishlist\Controller\WishlistProvider::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation disabled
     * @magentoAppArea frontend
     */
    public function testItUpdatesQuantityOfExistingItemAsGuest()
    {
        $product = $this->productRepository->get('simple');

        $this->performPostRequest('wishlist/index/add', ['product' => $product->getId()]);

        $item = $this->getFirstGuestWishlistItem();
        $this->assertNotNull($item, 'Guest wishlist item was not created.');
        $this->assertEquals(1, (int) $item->getQty());

        $this->wishlistProvider->clearCache();

        $this->performPostRequest('wishlist/index/updateItemOptions', [
            'id' => $item->getId(),
            'product' => $product->getId(),
            'qty' => 3,
        ]);

        $updatedItem = $this->getFirstGuestWishlistItem();
        $this->assertNotNull($updatedItem, 'Guest wishlist item disappeared after update.');
        $this->assertEquals(3, (int) $updatedItem->getQty(), 'Updated quantity was not persisted for guest.');
    }

    protected function performPostRequest(string $uri, array $params): void
    {
        $this->getRequest()->setMethod(\Magento\Framework\App\Request\Http::METHOD_POST);
        $this->getRequest()->setPostValue(array_merge($params, ['form_key' => $this->formKey->getFormKey()]));
        $this->dispatch($uri);
        $this->getRequest()->setDispatched(false);
    }

    protected function getFirstGuestWishlistItem(): ?\Magento\Wishlist\Model\Item
    {
        $wishlist = $this->wishlistFactory->create();
        $wishlist->load($this->cookieManager->getCookie('wishlist'), 'sharing_code');

        if (!$wishlist->getId()) {
            return null;
        }

        $items = $wishlist->getItemCollection()->clear()->load()->getItems();

        return array_shift($items) ?: null;
    }
}
