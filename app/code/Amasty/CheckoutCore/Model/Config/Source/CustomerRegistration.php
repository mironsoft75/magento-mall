<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomerRegistration implements OptionSourceInterface
{
    public const NO = '0';
    public const AFTER_PLACING  = '1';
    public const OPTIONAL = '2';
    public const REQUIRED = '3';
    
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::NO, 'label' => __('No')],
            ['value' => self::AFTER_PLACING, 'label' => __('After Placing an Order')],
            ['value' => self::OPTIONAL, 'label' => __('While Placing an Order, Optional')],
            ['value' => self::REQUIRED, 'label' => __('While Placing an Order, Required')]
        ];
    }
}
