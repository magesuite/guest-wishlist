<?php

namespace MageSuite\GuestWishlist\Controller;

class WishlistProvider implements \Magento\Wishlist\Controller\WishlistProviderInterface
{
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
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
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
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager */
                $cookieManager = $objectManager->get(\Magento\Framework\Stdlib\CookieManagerInterface::class);

                if ($cookieManager->getCookie('wishlist')) {
                    $wishlist->load($cookieManager->getCookie('wishlist'), 'sharing_code');
                }

                if ($wishlist->getId()) {
                    return $wishlist;
                }

                $wishlist->setCustomerId(0);
                $wishlist->setSharingCode($objectManager->get(\Magento\Framework\Math\Random::class)->getUniqueHash());
                $wishlist->save();

                $cookieMetadataManager = $objectManager->get(\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class);
                /** @var \Magento\Framework\Session\SessionManagerInterface $sessionManager */
                $sessionManager = $objectManager->get(\Magento\Framework\Session\SessionManagerInterface::class);
                $metadata = $cookieMetadataManager
                    ->createPublicCookieMetadata()
                    ->setPath($sessionManager->getCookiePath())
                    ->setDomain($sessionManager->getCookieDomain())
                    ->setDuration(86400 * 365);

                $cookieManager->setPublicCookie('wishlist', $wishlist->getSharingCode(), $metadata);

                $this->wishlist = $wishlist;

                return $wishlist;
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
}