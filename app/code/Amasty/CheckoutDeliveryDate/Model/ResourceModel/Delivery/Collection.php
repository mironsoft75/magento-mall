<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Model\ResourceModel\Delivery;

use Amasty\CheckoutDeliveryDate\Model\Delivery;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Delivery::class, \Amasty\CheckoutDeliveryDate\Model\ResourceModel\Delivery::class);
    }

    /**
     * @param array $quoteIds
     */
    public function addSizeSelectByQuoteIds(array $quoteIds = []): void
    {
        $this->addFieldToFilter('quote_id', ['in' => $quoteIds]);
        $this->getSelect()->reset(Select::COLUMNS);
        $this->getSelect()->columns(['size' => new \Zend_Db_Expr('COUNT(*)')]);
    }
}
