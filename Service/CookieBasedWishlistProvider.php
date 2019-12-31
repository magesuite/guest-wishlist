<?php

namespace MageSuite\GuestWishlist\Service;

class CookieBasedWishlistProvider
{
    const SECONDS_IN_MINUTE = 60;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var \MageSuite\GuestWishlist\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Math\Random $random,
        \MageSuite\GuestWishlist\Helper\Configuration $configuration
    ) {
        $this->cookieManager = $cookieManager;
        $this->wishlistFactory = $wishlistFactory;
        $this->random = $random;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->configuration = $configuration;
    }

    public function getWishlist($createNew = true)
    {
        $wishlist = $this->wishlistFactory->create();

        if ($this->cookieManager->getCookie('wishlist')) {
            $wishlist->load($this->cookieManager->getCookie('wishlist'), 'sharing_code');
        }

        if ($wishlist->getId()) {
            return $wishlist;
        }

        if (!$createNew) {
            return null;
        }

        $wishlist->setCustomerId(0);
        $wishlist->setSharingCode($this->random->getUniqueHash());
        $wishlist->save();

        $this->setCookieWithSharingCode($wishlist->getSharingCode());

        return $wishlist;
    }

    public function setCookieWithSharingCode($sharingCode)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($this->configuration->getGuestWishlistCookieLifetime()*self::SECONDS_IN_MINUTE);

        $this->cookieManager->setPublicCookie('wishlist', $sharingCode, $metadata);
    }
}
