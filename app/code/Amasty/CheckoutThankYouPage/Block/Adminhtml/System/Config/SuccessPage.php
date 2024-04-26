<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutThankYouPage
*/

declare(strict_types=1);

namespace Amasty\CheckoutThankYouPage\Block\Adminhtml\System\Config;

use Amasty\CheckoutThankYouPage\Model\ThankYouPageModule;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SuccessPage extends Field
{
    /**
     * @var ThankYouPageModule
     */
    private $thankYouPageModule;

    public function __construct(
        Context $context,
        ThankYouPageModule $thankYouPageModule,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->thankYouPageModule = $thankYouPageModule;
    }
    
    protected function _getElementHtml(AbstractElement $element): string
    {
        if ($this->thankYouPageModule->isModuleEnable($element->getScope(), (int)$element->getScopeId())) {
            $element->setDisabled('disabled');
        }

        return $element->getElementHtml();
    }
}
