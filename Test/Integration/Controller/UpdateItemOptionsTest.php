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
    protected ?\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
        $this->productRepository = $this->_objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->cookieManager = $this->_objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);
        $this->wishlistFactory = $this->_objectManager->get(\Magento\Wishlist\Model\WishlistFactory::class);
        $this->wishlistProvider = $this->_objectManager->get(\MageSuite\GuestWishlist\Controller\WishlistProvider::class);
        $this->cookieBasedWishlistProvider = $this->_objectManager->get(\MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider::class);
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

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture MageSuite_GuestWishlist::Test/Integration/_files/multiple_wishlists.php
     * @magentoDbIsolation disabled
     * @magentoAppArea frontend
     */
    public function testItCannotUpdateCustomerOwnedItemViaStolenSharingCode()
    {
        $customerWishlist = $this->wishlistFactory->create();
        $customerWishlist->load('customer_wishlist', 'sharing_code');
        $this->assertNotEquals(0, (int) $customerWishlist->getCustomerId(), 'Fixture wishlist must be customer-owned.');

        $items = $customerWishlist->getItemCollection()->getItems();
        $item = array_shift($items);
        $this->assertNotNull($item, 'Fixture customer wishlist item is missing.');

        $originalQty = (int) $item->getQty();
        $productId = (int) $item->getProductId();

        // Attacker installs the customer's stolen sharing code as their own guest wishlist cookie,
        // exactly as MageSuite\GuestWishlist\Controller\Wishlist\Copy::execute() does with zero validation.
        $this->cookieBasedWishlistProvider->setCookieWithSharingCode('customer_wishlist');
        $this->wishlistProvider->clearCache();

        $this->performPostRequest('wishlist/index/updateItemOptions', [
            'id' => $item->getId(),
            'product' => $productId,
            'qty' => $originalQty + 99,
        ]);

        $reloadedItem = $this->_objectManager->create(\Magento\Wishlist\Model\Item::class);
        $reloadedItem->load($item->getId());

        $this->assertEquals(
            $originalQty,
            (int) $reloadedItem->getQty(),
            'A guest using a stolen customer sharing code must not be able to update that customer\'s wishlist item.'
        );
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
