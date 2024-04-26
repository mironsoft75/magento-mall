<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\ConfigManagement\ConfigToAttribute\Processor;

use Magento\Customer\Model\Attribute;

interface ProcessorInterface
{
    /**
     * @param Attribute $attribute
     * @param string $value
     * @param int $websiteId
     * @return void
     */
    public function execute(Attribute $attribute, string $value, int $websiteId): void;
}
