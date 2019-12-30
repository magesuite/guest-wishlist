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

    public function isCustomerGuest()
    {
        return $this->customerSession->getCustomerId() == null;
    }

    /**
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getGuestWishlist()
    {
        return $this->cookieBasedWishlistProvider->getWishlist(true);
    }

    public function getCopyLink()
    {
        $guestWishlsit = $this->getGuestWishlist();

        return $this->url->getUrl('guest_wishlist/wishlist/copy', ['sharing_code' => $guestWishlsit->getSharingCode()]);
    }

    public function wishlistHasItems()
    {
        $guestWishlsit = $this->getGuestWishlist();

        return $guestWishlsit->getItemsCount() > 0;
    }
}