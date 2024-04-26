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

class Wheel extends Generic
{
    /**
     * @var $_template
     */
    protected $_template = 'tab/wheel.phtml';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $_urlInterface;

    /**
     * @var \Webkul\SpinToWin\Model\Info
     */
    public $spinModel;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Webkul\SpinToWin\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_urlInterface = $urlInterface;
        $this->helper = $helper;
        $this->spinModel = $this->_coreRegistry->registry('spininfo');
    }

    /**
     * Wheel
     *
     * @return \Webkul\SpinToWin\Model\Wheel
     */
    public function getWheel()
    {
        return $this->spinModel->getWheel();
    }

    /**
     * URL
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->_urlInterface->getUrl('catalog/product_gallery/upload');
    }

    /**
     * Get path of uploaded images
     *
     * @return \Magento\Framework\UrlInterface
     */
    /**
     * Get Media Url
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

    /**
     * Spin helper
     *
     * @return void
     */
    public function spinHelper()
    {
        return $this->helper;
    }
}
