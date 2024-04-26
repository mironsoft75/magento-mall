<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Model\Rewrite;

class RuleQuoteCollection extends \Magento\SalesRule\Model\ResourceModel\Rule\Quote\Collection
{
    /**
     * To hide campaign rules
     */
    public function _renderFiltersBefore()
    {
        $this->getSelect()->where('main_table.user_system = 0');
        parent::_renderFiltersBefore();
    }
}
