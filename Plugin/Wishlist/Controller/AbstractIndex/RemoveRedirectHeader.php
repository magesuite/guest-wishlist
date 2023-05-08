<?php
declare(strict_types=1);

namespace MageSuite\GuestWishlist\Plugin\Wishlist\Controller\AbstractIndex;

class RemoveRedirectHeader
{
    protected \Magento\Wishlist\Model\AuthenticationStateInterface $authenticationState;
    protected \Magento\Customer\Model\Session $customerSession;
    protected \Magento\Framework\App\Response\Http $response;

    public function __construct(
        \Magento\Wishlist\Model\AuthenticationStateInterface $authenticationState,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http $response
    ) {
        $this->authenticationState = $authenticationState;
        $this->customerSession = $customerSession;
        $this->response = $response;
    }

    public function beforeDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (!$this->authenticationState->isEnabled()
            && !$this->customerSession->authenticate()
            && $this->response->isRedirect()
        ) {
            $this->response->setStatusHeader(200)
                ->clearHeader('Location');
        }
    }
}
