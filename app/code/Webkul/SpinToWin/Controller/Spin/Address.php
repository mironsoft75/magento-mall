<?php

namespace Webkul\SpinToWin\Controller\Spin;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Webkul\SpinToWin\Model\ResourceModel\Reports\CollectionFactory as ReportsCollectionFactory;
use Silk\Integrations\Model\Session as UserSession;
use Magento\Framework\Exception\NoSuchEntityException;

class Address implements ActionInterface,HttpGetActionInterface{
    protected $_pageFactory;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ReportsCollectionFactory $ReportsCollectionFactory,
        UserSession $userSession,
		\Magento\Framework\View\Result\PageFactory $pageFactory
        )
	{
        $this->_request = $context->getRequest();
        $this->resultRedirectFactory= $context->getResultRedirectFactory();
		$this->_pageFactory = $pageFactory;
        $this->ReportsCollectionFactory = $ReportsCollectionFactory;
        $this->userSession = $userSession;
	}

    public function execute()
    {
        $reportsId = $this->_request->getParam('reports_id');
        $customerId = $this->userSession->getId();
        $collection = $this->ReportsCollectionFactory->create();
        $collection->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('entity_id',$reportsId);
        $collection->getSelect()->joinLeft(['address' => $collection->getTable('spintowin_product_address')],
        'main_table.entity_id = address.reports_id','address.*')->limit(1);
        $c = $collection->getFirstItem();
        if(!$c->hasData()){
            throw new NoSuchEntityException(__("Spin Win Not Found %1",$reportsId));
        }
        if(!empty($c->getAddressId())){
            return $this->resultRedirectFactory->create()->setPath('*/*/detail/reports_id/'.$reportsId);
        }else {
            $page = $this->_pageFactory->create();
            $block = $page->getLayout()->getBlock('spintowin_address');
            $block->setReportsId($reportsId);
            return $page;
        }
    }
}
