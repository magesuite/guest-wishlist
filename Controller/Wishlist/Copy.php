<?php

namespace MageSuite\GuestWishlist\Controller\Wishlist;

class Copy extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        parent::__construct($context);

        $this->cookieBasedWishlistProvider = $cookieBasedWishlistProvider;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute()
    {
        $redirect = $this->redirectFactory->create();
        $sharingCode = $this->_request->getParam('sharing_code');

        if (!empty($sharingCode)) {
            $this->cookieBasedWishlistProvider->setCookieWithSharingCode($sharingCode);
        }

        return $redirect->setPath('wishlist/index/index');
    }
}
