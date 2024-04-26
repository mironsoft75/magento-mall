<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutLayoutBuilder
*/

declare(strict_types=1);

namespace Amasty\CheckoutLayoutBuilder\Block\Config;

use Amasty\CheckoutLayoutBuilder\Model\Config\CheckoutBlocksProvider;
use Amasty\CheckoutLayoutBuilder\Model\ConfigProvider;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;

/**
 * Change container template of config field to custom template
 * @method AbstractElement getElement()
 */
class LayoutBuilderArea extends Field
{
    public const MODERN_DESIGN = '1';

    /**
     * @var CheckoutBlocksProvider
     */
    private $checkoutBlocksProvider;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        CheckoutBlocksProvider $checkoutBlocksProvider,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutBlocksProvider = $checkoutBlocksProvider;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_template = 'Amasty_CheckoutLayoutBuilder::system/config/form/field/layout_builder_area.phtml';

        parent::_construct();
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $this->setElement($element);
        return $this->_decorateRowHtml($element, $this->_toHtml());
    }

    /**
     * @param AbstractElement $element
     * @param string $html
     * @return string
     */
    public function _decorateRowHtml(AbstractElement $element, $html): string
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);
        $colspan = "3";

        if ($isCheckboxRequired) {
            $colspan = "4";
        }
        $html = '<td colspan="' . $colspan . '">' . $html . '</td>';
        return parent::_decorateRowHtml($element, $html);
    }

    /**
     * @return array
     */
    public function getBlockDefaultNames(): array
    {
        return $this->checkoutBlocksProvider->getDefaultBlockTitles();
    }

    /**
     * @return array
     */
    public function getConfigForUseDefault(): array
    {
        $scope = $this->getElement()->getScope();
        $scopeId = $this->getElement()->getScopeId();
        list($parentScope, $parentScopeId) = $this->getParentScopeAndScopeId($scope, (int)$scopeId);
        $design = $this->scopeConfig->getValue(
            ConfigProvider::PATH_PREFIX
                . ConfigProvider::DESIGN_BLOCK
                . ConfigProvider::FIELD_CHECKOUT_DESIGN,
            $parentScope,
            $parentScopeId
        );

        $layoutField = ConfigProvider::FIELD_CHECKOUT_LAYOUT;
        if ($design == self::MODERN_DESIGN) {
            $layoutField = ConfigProvider::FIELD_CHECKOUT_LAYOUT_MODERN;
        }

        $layout = $this->scopeConfig->getValue(
            ConfigProvider::PATH_PREFIX
                . ConfigProvider::DESIGN_BLOCK
                . $layoutField,
            $parentScope,
            $parentScopeId
        );

        return [
            'design' => (int)$design,
            'layout' => $layout,
        ];
    }

    /**
     * @param string $scope
     * @param int $scopeId
     * @return array
     */
    private function getParentScopeAndScopeId(string $scope, int $scopeId): array
    {
        $parentScope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $parentScopeId = 0;
        if ($scope == ScopeInterface::SCOPE_STORES) {
            $parentScope = ScopeInterface::SCOPE_WEBSITE;
            $parentScopeId = $this->_storeManager->getStore($scopeId)->getWebsiteId();
        }

        return [$parentScope, $parentScopeId];
    }
}
