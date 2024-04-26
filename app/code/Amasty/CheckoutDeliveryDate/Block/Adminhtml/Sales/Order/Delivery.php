<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Block\Adminhtml\Sales\Order;

class Delivery extends \Amasty\CheckoutDeliveryDate\Block\Sales\Order\Info\Delivery
{
    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Amasty_CheckoutDeliveryDate::sales/order/delivery.phtml');
    }
}
