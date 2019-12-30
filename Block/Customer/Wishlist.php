<?php

namespace MageSuite\GuestWishlist\Block\Customer;

class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist
{
    /**
     * Parent block is checking customer authorization
     * This method disables it by calling original code from Template class
     */
    public function _toHtml()
    {
        if (!$this->getTemplate()) {
            return '';
        }

        return $this->fetchView($this->getTemplateFile());
    }
}
