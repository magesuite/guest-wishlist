<?php

namespace MageSuite\GuestWishlist\Model\Wishlist;

class AuthenticationState implements \Magento\Wishlist\Model\AuthenticationStateInterface
{
    public function isEnabled()
    {
        return false;
    }
}
