<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Service;

class CookieBasedWishlistProvider
{
    public const SECONDS_IN_MINUTE = 60;

    protected \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager;

    protected \Magento\Wishlist\Model\WishlistFactory $wishlistFactory;

    protected \Magento\Framework\Math\Random $random;

    protected \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory;

    protected \Magento\Framework\Session\SessionManagerInterface $sessionManager;

    protected \MageSuite\GuestWishlist\Helper\Configuration $configuration;

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

    public function getWishlist(bool $createNew = true): ?\Magento\Wishlist\Model\Wishlist
    {
        $wishlist = $this->wishlistFactory->create();

        if ($this->cookieManager->getCookie('wishlist')) {
            $wishlist->load($this->cookieManager->getCookie('wishlist'), 'sharing_code');
        }

        if ($wishlist->getId() && (int) $wishlist->getCustomerId() === 0) {
            return $wishlist;
        }

        if (!$createNew) {
            return null;
        }

        $newWishlist = $this->wishlistFactory->create();
        $newWishlist->setCustomerId(0);
        $newWishlist->setSharingCode($this->random->getUniqueHash());
        $newWishlist->save();

        $this->setCookieWithSharingCode($newWishlist->getSharingCode());

        return $newWishlist;
    }

    public function setCookieWithSharingCode(string $sharingCode): void
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($this->configuration->getGuestWishlistCookieLifetime() * self::SECONDS_IN_MINUTE)
            ->setSecure(true);

        $this->cookieManager->setPublicCookie('wishlist', $sharingCode, $metadata);
    }
}
