<?php

namespace Webkul\SpinToWin\Controller\Spin;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;

class Record implements ActionInterface,HttpGetActionInterface{
    protected $_pageFactory;

	public function __construct(
		\Magento\Framework\View\Result\PageFactory $pageFactory
        )
	{
		$this->_pageFactory = $pageFactory;
	}

    public function execute()
    {
        return $this->_pageFactory->create();
    }

}