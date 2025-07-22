<?php

declare(strict_types=1);

namespace MageSuite\GuestWishlist\Helper;

class Configuration
{
    public const GUEST_WISHLIST_COOKIE_LIFETIME_XML_PATH = 'guest_wishlist/general/cookie_lifetime';
    public const GUEST_WISHLIST_SHOW_ACCOUNT_LINKS_FOR_GUEST_XML_PATH = 'guest_wishlist/general/show_account_links_for_guest';
    public const GUEST_WISHLIST_EMPTY_WISHLISTS_RETENTION_PERIOD = 'guest_wishlist/general/empty_wishlists_retention_period';

    public function __construct(protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
    }

    public function getGuestWishlistCookieLifetime(): int
    {
        return (int)$this->scopeConfig->getValue(self::GUEST_WISHLIST_COOKIE_LIFETIME_XML_PATH);
    }

    public function getGuestWishlistCookieLifetimeInDays(): int
    {
        $lifetimeInMinutes = $this->getGuestWishlistCookieLifetime();

        return $lifetimeInMinutes > 0 ? (int)($lifetimeInMinutes / 1440) : 0;
    }

    public function isShowAccountLinksForGuest(): bool
    {
        return $this->scopeConfig->isSetFlag(self::GUEST_WISHLIST_SHOW_ACCOUNT_LINKS_FOR_GUEST_XML_PATH);
    }

    public function getUseQtyInWishlist(): bool
    {
        return $this->scopeConfig->isSetFlag(
            \Magento\Wishlist\Helper\Data::XML_PATH_WISHLIST_LINK_USE_QTY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getEmptyWishlistsRetentionPeriod(): int
    {
        return (int)$this->scopeConfig->getValue(self::GUEST_WISHLIST_EMPTY_WISHLISTS_RETENTION_PERIOD);
    }
}
