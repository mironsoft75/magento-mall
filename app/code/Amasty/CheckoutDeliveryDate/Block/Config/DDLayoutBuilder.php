<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Block\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Add new type of field renderer - hidden field
 * @method AbstractElement getElement()
 */
class DDLayoutBuilder extends Field
{
    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct(): void
    {
        $this->_template = 'Amasty_CheckoutDeliveryDate::system/config/form/field/dd_layout_builder.phtml';
        parent::_construct();
    }

    /**
     * Get the grid and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $this->setElement($element);

        return $this->_toHtml();
    }
}
