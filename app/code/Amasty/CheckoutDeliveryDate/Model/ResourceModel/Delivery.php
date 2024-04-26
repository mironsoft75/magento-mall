<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Delivery extends AbstractDb
{
    public const MAIN_TABLE = 'amasty_amcheckout_delivery';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}
