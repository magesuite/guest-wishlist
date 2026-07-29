<?php

namespace MageSuite\GuestWishlist\Model\ViewModel;

class CopyWishlistLink implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->customerSession = $customerSession;
        $this->cookieBasedWishlistProvider = $cookieBasedWishlistProvider;
        $this->url = $url;
    }

    public function isCustomerGuest(): bool
    {
        return !$this->customerSession->isLoggedIn();
    }

    public function getGuestWishlist(): ?\Magento\Wishlist\Model\Wishlist
    {
        return $this->cookieBasedWishlistProvider->getWishlist(false);
    }

    public function getCopyLink(): string
    {
        $guestWishlist = $this->getGuestWishlist();
        if (!$guestWishlist) {
            return '';
        }

        return $this->url->getUrl('guest_wishlist/wishlist/copy', ['sharing_code' => $guestWishlist->getSharingCode()]);
    }

    public function wishlistHasItems(): bool
    {
        $guestWishlist = $this->getGuestWishlist();
        return $guestWishlist && $guestWishlist->getItemsCount() > 0;
    }
}
