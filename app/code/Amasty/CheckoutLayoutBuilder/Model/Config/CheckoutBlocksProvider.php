<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutLayoutBuilder
*/

declare(strict_types=1);

namespace Amasty\CheckoutLayoutBuilder\Model\Config;

use Amasty\CheckoutCore\Api\CheckoutBlocksProviderInterface;
use Amasty\CheckoutLayoutBuilder\Model\ConfigProvider;

class CheckoutBlocksProvider implements CheckoutBlocksProviderInterface
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
     * @return array
     */
    public function getDefaultBlockTitles(): array
    {
        return [
            'shipping_address' => __('Shipping Address'),
            'shipping_method' => __('Shipping Method'),
            'delivery' => __('Delivery'),
            'payment_method' => __('Payment Method'),
            'summary' => __('Order Summary'),
        ];
    }

    /**
     * @param ?int $store
     * @return array
     */
    public function getBlocksConfig(int $store = null): array
    {
        return $this->configProvider->getCheckoutBlocksConfig($store);
    }
}
