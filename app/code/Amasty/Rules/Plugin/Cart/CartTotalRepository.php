<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Special Promotions Base for Magento 2
 */

namespace Amasty\Rules\Plugin\Cart;

use Magento\Framework\App\ProductMetadataInterface;
use Amasty\Rules\Model\Registry;
use Amasty\Rules\Model\DiscountRegistry;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Amasty\Rules\Model\ConfigModel;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\RuleFactory;

/**
 * Fix Magento bug on checkout API.
 * Insert discount breakdown data.
 */
class CartTotalRepository
{
    public const REGISTRY_IGNORE_EXTENSION_ATTRIBUTES_KEY = 'amasty_checkout_ignore_extension_attributes';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var TotalsExtensionFactory
     */
    private $totalsExtensionFactory;

    /**
     * @var DiscountRegistry
     */
    private $discountRegistry;

    /**
     * @var \Amasty\Rules\Model\ConfigModel
     */
    private $configModel;

    public function __construct(
        Registry $registry,
        ProductMetadataInterface $productMetadata,
        CouponFactory $coupon,
        RuleFactory $rule,
        DiscountRegistry $discountRegistry,
        TotalsExtensionFactory $totalsExtensionFactory,
        ConfigModel $configModel
    )
    {
        $this->rule = $rule;
        $this->coupon = $coupon;
        $this->registry = $registry;
        $this->productMetadata = $productMetadata;
        $this->totalsExtensionFactory = $totalsExtensionFactory;
        $this->discountRegistry = $discountRegistry;
        $this->configModel = $configModel;
    }

    /**
     * Fix Magento bug on checkout API
     * @param \Magento\Quote\Model\Cart\CartTotalRepository $subject
     * @param int|string $cartId
     *
     * @return array
     *
     * @see \Amasty\Rules\Plugin\framework\Api\DataObjectHelperPlugin::beforePopulateWithArray
     *
     */
    public function beforeGet(\Magento\Quote\Model\Cart\CartTotalRepository $subject, $cartId)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.4', '<')) {
            $this->registry->register(self::REGISTRY_IGNORE_EXTENSION_ATTRIBUTES_KEY, true, true);
        }

        return [$cartId];
    }

    /**
     * Fix Magento bug on checkout API
     *
     * @param \Magento\Quote\Model\Cart\CartTotalRepository $subject
     * @param \Magento\Quote\Model\Cart\Totals $quoteTotals
     *
     * @return \Magento\Quote\Model\Cart\Totals
     *
     * @see \Amasty\Rules\Plugin\framework\Api\DataObjectHelperPlugin::beforePopulateWithArray
     *
     */
    public function afterGet(\Magento\Quote\Model\Cart\CartTotalRepository $subject, $quoteTotals)
    {
        $this->registry->unregister(self::REGISTRY_IGNORE_EXTENSION_ATTRIBUTES_KEY);

        if ($this->configModel->getShowDiscountBreakdown() && $this->discountRegistry->restoreDataForBreakdown()) {
            $extensionAttributes = $quoteTotals->getExtensionAttributes();

            if (!$extensionAttributes) {
                $extensionAttributes = $this->totalsExtensionFactory->create();
            }

            $extensionAttributes->setAmruleDiscountBreakdown($this->discountRegistry->getRulesWithAmount());
            $quoteTotals->setExtensionAttributes($extensionAttributes);

            $this->discountRegistry->flushDiscount();
        }

        if ($quoteTotals->getCouponCode()) {
            $segments = $quoteTotals->getTotalSegments();
            if (!empty($segments) && !empty($segments['discount'])) {
                $title = $segments['discount']->getTitle();

                if ($title) {
                    $extensionAttributes = $quoteTotals->getExtensionAttributes();
                    $extensionAttributes->setCouponLabel($title);
                    $quoteTotals->setExtensionAttributes($extensionAttributes);
                }
            }
        }

        return $quoteTotals;
    }
}
