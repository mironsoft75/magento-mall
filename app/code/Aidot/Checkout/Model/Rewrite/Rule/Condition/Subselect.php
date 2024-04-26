<?php

namespace Aidot\Checkout\Model\Rewrite\Rule\Condition;

class Subselect extends \Magento\SalesRule\Model\Rule\Condition\Product\Subselect {
    /**
     * Validate
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if (!$this->getConditions()) {
            return false;
        }
        $attr = $this->getAttribute();
        $total = 0;
        foreach ($model->getQuote()->getAllVisibleItems() as $item) {
            if(!$item->getIsActive() || $item->getFlashSale()){
                continue;
            }
            $hasValidChild = false;
            $useChildrenTotal = ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE);
            $childrenAttrTotal = 0;
            $children = $item->getChildren();
            if (!empty($children)) {
                foreach ($children as $child) {
                    if (parent::validate($child)) {
                        $hasValidChild = true;
                        if ($useChildrenTotal) {
                            $childrenAttrTotal += $child->getData($attr);
                        }
                    }
                }
            }
            if ($hasValidChild || parent::validate($item)) {
                $total += ($hasValidChild && $useChildrenTotal)
                    ? $childrenAttrTotal * $item->getQty()
                    : $item->getData($attr);
            }
        }
        return $this->validateAttribute($total);
    }
}