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
        $this->getRequest()->setPostValue(['form_key' => $formKey->getFormKey()]);

        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
        $productRepository = $this->_objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);

        $product = $productRepository->get('simple');

        $this->dispatch('wishlist/index/add/product/' . $product->getId());

        $this->assertSessionMessages(
            $this->contains('Simple Product has been added to your Wish List.'),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
