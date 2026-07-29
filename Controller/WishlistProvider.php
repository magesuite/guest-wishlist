<?php

namespace MageSuite\GuestWishlist\Controller;

class WishlistProvider implements \Magento\Wishlist\Controller\WishlistProviderInterface
{
    public const ACTIONS_ALLOWED_TO_CREATE_WISHLIST = [
        'wishlist_index_add',
        'wishlist_index_fromcart',
    ];

    public const ACTIONS_ALLOWED_TO_RENDER_EMPTY_WISHLIST = [
        'wishlist_index_index',
    ];

    /**
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $wishlist;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider
     */
    protected $cookieBasedWishlistProvider;

    public function __construct(
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\GuestWishlist\Service\CookieBasedWishlistProvider $cookieBasedWishlistProvider
    ) {
        $this->request = $request;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->cookieBasedWishlistProvider = $cookieBasedWishlistProvider;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getWishlist($wishlistId = null)
    {
        if ($this->wishlist) {
            return $this->wishlist;
        }
        try {
            if (!$wishlistId) {
                $wishlistId = $this->request->getParam('wishlist_id');
            }
            $customerId = $this->customerSession->getCustomerId();
            $wishlist = $this->wishlistFactory->create();

            if (!$customerId) {
                $guestWishlist = $this->cookieBasedWishlistProvider->getWishlist($this->shouldCreateWishlistForGuest());

                if (!$guestWishlist && $this->shouldRenderEmptyWishlistForGuest()) {
                    $guestWishlist = $wishlist;
                }

                if ($wishlistId && (!$guestWishlist || (int) $guestWishlist->getId() !== (int) $wishlistId)) {
                    return false;
                }

                $this->wishlist = $guestWishlist;

                return $this->wishlist;
            }

            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } elseif ($customerId) {
                $wishlist->loadByCustomerId($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The requested Wish List doesn\'t exist.')
                );
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addError($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t create the Wish List right now.'));
            return false;
        }
        $this->wishlist = $wishlist;

        return $wishlist;
    }

    public function clearCache(): void
    {
        $this->wishlist = null;
    }

    protected function shouldCreateWishlistForGuest(): bool
    {
        return in_array($this->request->getFullActionName(), self::ACTIONS_ALLOWED_TO_CREATE_WISHLIST, true);
    }

    protected function shouldRenderEmptyWishlistForGuest(): bool
    {
        return in_array($this->request->getFullActionName(), self::ACTIONS_ALLOWED_TO_RENDER_EMPTY_WISHLIST, true);
    }
}
