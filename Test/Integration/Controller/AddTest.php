<?php

namespace MageSuite\GuestWishlist\Test\Integration\Controller;

class AddTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messages;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messages = $this->_objectManager->get(\Magento\Framework\Message\ManagerInterface::class);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     */
    public function testItIsPossibleToAddItemToWishlistAsGuest()
    {
        /** @var \Magento\Framework\Data\Form\FormKey $formKey */
        $formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
        $this->getRequest()->setMethod(\Magento\Framework\App\Request\Http::METHOD_POST);
        $this->getRequest()->setPostValue(['form_key' => $formKey->getFormKey()]);

        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);

        $product = $productRepository->get('simple');

        $this->dispatch('wishlist/index/add/product/' . $product->getId());

        $messages = $this->getMessages();
        $message = array_pop($messages);

        $assertContains = method_exists($this, 'assertStringContainsString') ? 'assertStringContainsString' : 'assertContains';

        $this->$assertContains('Simple Product has been added to your Wish List.', $message);
    }
}
