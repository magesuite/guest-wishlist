<?php

namespace MageSuite\GuestWishlist\Block\Customer\Wishlist\Guest;

class CopyWishlistLink extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_GuestWishlist::copy_wishlist_link.phtml';

    public function _toHtml()
    {
        if(!$this->getViewModel()->isCustomerGuest() || !$this->getViewModel()->wishlistHasItems()) {
            return '';
        }

        return parent::_toHtml();
    }
}