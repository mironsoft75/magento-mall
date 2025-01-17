<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Controller\Adminhtml\Manage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\SpinToWin\Model\ResourceModel\Info\CollectionFactory;

class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Webkul\SpinToWin\Logger\Logger
     */
    public $logger;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Webkul\SpinToWin\Helper\Data $helper
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
       
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection as $spins) {
                $spins->setStatus(1);
                $spins->save();
                $count++;
            }
            $this->helper->cacheFlush();
            $this->messageManager->addSuccess(__('A total of %1 spin(s) have been enabled.', $count));
        } catch (\Exception $e) {
            $this->logger->info(
                "MassDisable::excute ".$e->getMessage()
            );
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/manage/index');
    }

    /**
     * Check Permission
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_SpinToWin::manage');
    }
}
