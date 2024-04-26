<?php
namespace Webkul\SpinToWin\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\SpinToWin\Model\ResourceModel\Reports\CollectionFactory;
use Exception;

class Status extends Action {
     /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter, 
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try{
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $status = (int) $this->getRequest()->getParam('status');
            $rateChangeStatus = 0;
            foreach($collection as $reportModel){
                $reportModel->setStatus($status);
                ++$rateChangeStatus;
                $reportModel->save();
            }
             
        }catch(Exception $e){
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. %1', $e->getMessage())
            );
            $this->_redirect('spintowin/draw/index');
        }
        
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $rateChangeStatus));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('spintowin/draw/index');

       return $resultRedirect;
    }
}