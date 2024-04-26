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

namespace Webkul\SpinToWin\Controller\Adminhtml\Segment;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute
     *
     * @return \Webkul\SpinToWin\Block\Adminhtml\Segment\Edit
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $resultPage = $this->_resultPageFactory->create();
            $output = $resultPage->getLayout()
                                ->createBlock(\Webkul\SpinToWin\Block\Adminhtml\Segment\Edit::class)
                                ;
            $this->getResponse()->setBody($output->toHtml());
        } else {
            $spinId = $this->getRequest()->getParam('spin_id');
            $this->_redirect('*/manage/edit', ['id' =>$spinId]);
        }
    }

    /**
     * Check permission
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_SpinToWin::manage');
    }
    
    /**
     * Process Url Keys
     *
     * @return boolean
     */
    public function _processUrlKeys()
    {
        return true;
    }
}
