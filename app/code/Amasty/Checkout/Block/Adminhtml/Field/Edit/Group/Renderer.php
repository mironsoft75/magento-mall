<?php
declare(strict_types=1);

namespace Amasty\Checkout\Block\Adminhtml\Field\Edit\Group;

use Amasty\CheckoutCore\Block\Adminhtml\Field\Edit\Group\Renderer as CheckoutRender;

class Renderer extends CheckoutRender
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Checkout::widget/form/renderer/group.phtml';
}
