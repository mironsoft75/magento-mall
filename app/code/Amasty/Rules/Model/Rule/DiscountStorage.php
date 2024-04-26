<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Special Promotions Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\Rules\Model\Rule;

use Magento\SalesRule\Model\Rule\Action\Discount\Data;

class DiscountStorage
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @param Data $discountData
     * @param int $ruleId
     * @param float $discountAmount
     * @param int|float $itemsQty
     * @return void
     */
    public function resolveDiscountAmount(Data $discountData, int $ruleId, float $discountAmount, $itemsQty): void
    {
        if (!isset($this->storage[$ruleId])) {
            $this->storage[$ruleId]['discount_amount'] = $this->storage[$ruleId]['items_counter'] = 0;
        }
        $this->storage[$ruleId]['items_counter']++;
        $this->storage[$ruleId]['discount_amount'] += round($discountData->getAmount(), 2);

        if ($this->storage[$ruleId]['items_counter'] != $itemsQty) {
            return;
        }

        $diff = round($discountAmount - $this->storage[$ruleId]['discount_amount'], 2);
        $this->storage[$ruleId]['discount_amount'] = $this->storage[$ruleId]['items_counter'] = 0;

        if ($diff !== 0.00) {
            $discountData->setAmount($discountData->getAmount() + $diff);
            $discountData->setBaseAmount($discountData->getBaseAmount() + $diff);
        }
    }
}
