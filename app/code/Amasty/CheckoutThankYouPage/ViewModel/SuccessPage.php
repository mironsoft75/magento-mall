<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutThankYouPage
*/

declare(strict_types=1);

namespace Amasty\CheckoutThankYouPage\ViewModel;

use Amasty\CheckoutCore\Model\Config as CheckoutCoreConfig;
use Amasty\CheckoutThankYouPage\Model\Config;
use Amasty\CheckoutThankYouPage\Model\ThankYouPageModule;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SuccessPage implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $confug;

    /**
     * @var CheckoutCoreConfig
     */
    private $checkoutCoreConfig;

    /**
     * @var ThankYouPageModule
     */
    private $thankYouPageModule;

    public function __construct(
        Config $config,
        CheckoutCoreConfig $checkoutCoreConfig,
        ThankYouPageModule $thankYouPageModule
    ) {
        $this->confug = $config;
        $this->checkoutCoreConfig = $checkoutCoreConfig;
        $this->thankYouPageModule = $thankYouPageModule;
    }

    public function isEnable(): bool
    {
        return $this->confug->isCustomPageEnable()
            && $this->checkoutCoreConfig->isEnabled()
            && !$this->thankYouPageModule->isModuleEnable();
    }
}
