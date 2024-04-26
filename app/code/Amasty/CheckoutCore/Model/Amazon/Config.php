<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Amazon;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XPATH_IS_ENABLED = 'payment/amazon_payment_v2/active';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isV2Enabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XPATH_IS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
