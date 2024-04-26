<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutThankYouPage
*/

declare(strict_types=1);

namespace Amasty\CheckoutThankYouPage\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class Config extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     *
     * @var string
     */
    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * Path Prefix For Config
     */
    public const PATH_PREFIX = 'amasty_checkout/';

    public const SUCCESS_CUSTOM_BLOCK = 'success_page/';

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getSuccessCustomBlockId(int $storeId = null): int
    {
        return (int)$this->getValue(self::SUCCESS_CUSTOM_BLOCK . 'block_id', $storeId);
    }
    
    public function isCustomPageEnable(int $storeId = null): bool
    {
        return (bool)$this->getValue(self::SUCCESS_CUSTOM_BLOCK . 'enabled_success_page', $storeId);
    }
}
