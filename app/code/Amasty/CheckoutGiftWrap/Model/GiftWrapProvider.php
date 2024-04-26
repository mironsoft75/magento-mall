<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutGiftWrap
*/

declare(strict_types=1);

namespace Amasty\CheckoutGiftWrap\Model;

use Amasty\CheckoutCore\Api\GiftWrapProviderInterface;

class GiftWrapProvider implements GiftWrapProviderInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @return bool
     */
    public function isGiftWrapEnabled(): bool
    {
        return $this->configProvider->isGiftWrapEnabled();
    }

    /**
     * @return float
     */
    public function getGiftWrapFee(): float
    {
        return $this->configProvider->getGiftWrapFee();
    }
}
