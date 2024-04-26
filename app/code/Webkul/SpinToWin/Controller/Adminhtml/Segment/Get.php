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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Get extends Action
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param \Webkul\SpinToWin\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Webkul\SpinToWin\Logger\Logger $logger,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->_jsonData = $jsonData;
        $this->helper = $helper;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $spinId = $this->getRequest()->getParam('spin-id');
            if ($spinId) {
                $wheel = $this->helper->getWheelData($spinId);
                $this->getResponse()->setHeader('Content-type', 'application/javascript');
                $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 1,
                            'data' => $wheel->getData()
                        ]
                    ));
                return;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Spin Id not found in param.'));
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getTraceAsString());
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 0,
                            'message' => $e->getMessage()
                        ]
                    ));
            return;
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
