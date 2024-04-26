<?php
declare(strict_types=1);

namespace Amasty\Checkout\Model;

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
    
    public const DESIGN_BLOCK = 'design/';
    
    public const OPTIONAL_UI_ELEMENTS_PATH = 'design/optional_ui_elements/';

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getAddressCheckboxState(int $storeId = null): string
    {
        return $this->getValue(self::DESIGN_BLOCK. 'address_checkbox_state', $storeId);
    }
    
    public function isCustomPlaceButtonText(int $storeId = null): bool
    {
        return $this->isSetFlag(self::DESIGN_BLOCK . 'custom_place_order', $storeId);
    }

    public function getPlaceButtonText(int $storeId = null): string
    {
        return $this->getValue(self::DESIGN_BLOCK . 'custom_place_order_text', $storeId);
    }
    
    public function isTooltipEnable(string $key, int $storeId = null): bool
    {
        return $this->isSetFlag(self::OPTIONAL_UI_ELEMENTS_PATH . $key . '_tooltip', $storeId);
    }

    public function getTooltipText(string $key, int $storeId = null): string
    {
        return $this->getValue(self::OPTIONAL_UI_ELEMENTS_PATH . $key . '_tooltip_text', $storeId);
    }
}
