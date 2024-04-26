<?php
namespace Webkul\SpinToWin\Controller\Adminhtml\Report;
use Magento\Backend\Helper\Data as BackendHelper;

abstract class AbstractReport extends \Magento\Backend\App\Action{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Webkul_SpinToWin::manage';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    /**
     * @var BackendHelper
     */
    private $backendHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        BackendHelper $backendHelperData
    )
    {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->backendHelper = $backendHelperData;
    }

     /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }

        $params = $this->initFilterData();

        foreach ($blocks as $block) {
            if ($block) {
                $block->setFilterData($params);
            }
        }

        return $this;
    }
    /**
     * Init filter data
     *
     * @return \Magento\Framework\DataObject
     */
    private function initFilterData(): \Magento\Framework\DataObject
    {
        $requestData = $this->backendHelper
            ->prepareFilterString(
                $this->getRequest()->getParam('filter')
            );

        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        return $params;
    }

}