<?php
namespace Webkul\SpinToWin\Controller\Adminhtml\Code;

use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\SpinToWin\Model\ResourceModel\Code\CollectionFactory;
use Webkul\SpinToWin\Controller\Adminhtml\Action as AdminAction;
use Exception;

class MassDelete extends AdminAction {
     /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
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
            $rateChangeStatus = 0;
            foreach ($collection as $rate) {
                $rate->delete();
                $rateChangeStatus++;
            }  
        }catch(Exception $e){
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. %1', $e->getMessage())
            );
            $this->_redirect('spintowin/code/index');
        }
        
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $rateChangeStatus));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

       return $resultRedirect;
    }
}