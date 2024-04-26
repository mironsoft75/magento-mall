<?php

namespace Webkul\SpinToWin\Controller\Spin;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Silk\Integrations\Model\Session as UserSession;
use Magento\Customer\Model\Url as CustomerUrl;

class Index implements ActionInterface,HttpGetActionInterface{
    protected $_pageFactory;
    protected $userSession;
    protected $resultRedirectFactory;
    protected $customerUrl;
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        UserSession $userSession,
        CustomerUrl $customerUrl,
		\Magento\Framework\View\Result\PageFactory $pageFactory
        )
	{
        $this->_request = $context->getRequest();
        $this->resultRedirectFactory= $context->getResultRedirectFactory();
        $this->userSession = $userSession;
        $this->customerUrl = $customerUrl;
		$this->_pageFactory = $pageFactory;
	}

    public function execute()
    {
        if($this->userSession->isLoggedIn()){
             $spinId = $this->_request->getParam('spin_id');
            $page = $this->_pageFactory->create();
            $block = $page->getLayout()->getBlock('spintowin_index');
            $block->setSpinId($spinId);
            return $page;
        }
        return $this->resultRedirectFactory->create()->setPath($this->customerUrl->getLoginUrl());
       
    }

}