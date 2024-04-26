<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\ConfigManagement\ConfigToField;

use Magento\Framework\App\Config\Value;

class GetAttributeCode
{
    /**
     * @var array<string, string>
     */
    private $aliases;

    /**
     * @param array<string, string> $aliases
     */
    public function __construct(array $aliases = [])
    {
        $this->aliases = $aliases;
    }

    public function execute(Value $configValue): string
    {
        return $this->aliases[$configValue->getPath()]
            ?? str_replace('_show', '', $configValue->getData('field'));
    }
}
