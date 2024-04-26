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

namespace Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Information extends Generic
{

    /**
     * @var $_template
     */
    protected $_template = 'tab/information.phtml';

    /**
     * @var \Webkul\SpinToWin\Model\Info
     */
    public $spinModel;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    public $websiteFactory;
     /**
     * @var \Magento\Framework\UrlInterface
     */
    public $_urlInterface;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->spinModel = $this->_coreRegistry->registry('spininfo');
        $this->websiteFactory = $websiteFactory;
        $this->_urlInterface = $urlInterface;
    }

    /**
     * Get Spin Info
     *
     * @return \Webkul\SpinToWin\Model\Info
     */
    public function getSpinInfo()
    {
        return $this->spinModel;
    }

    /**
     * Get Websites
     *
     * @return array
     */
    public function getWebsites()
    {
        return $this->websiteFactory->create()->getCollection()->toOptionArray();
    }
    /**
     * Get path of uploaded images
     *
     * @param mixed $filePath
     * @return \Magento\Framework\UrlInterface
     */
    public function getMediaUrl($filePath)
    {
        return $this->_urlBuilder->getBaseUrl([
            '_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ]).$filePath;
    }
}
