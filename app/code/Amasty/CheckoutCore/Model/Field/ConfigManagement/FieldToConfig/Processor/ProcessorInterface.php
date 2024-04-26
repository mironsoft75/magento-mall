<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\ConfigManagement\FieldToConfig\Processor;

use Amasty\CheckoutCore\Model\Field;

interface ProcessorInterface
{
    /**
     * @param Field $field
     * @param string $configPath
     * @return void
     */
    public function execute(Field $field, string $configPath): void;
}
