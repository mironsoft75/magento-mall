<?php
namespace Webkul\SpinToWin\Controller\Adminhtml\Code;

use Magento\Backend\App\Action;

class Index extends Action {

    protected $resultPageFactory = false;
    

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

    public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Cloud Server Code')));

		return $resultPage;
	}

	protected function _isAlloed()
	{
		return $this->_authorization->isAllowed('Webkul_SpinToWin::code_list');
	}
}