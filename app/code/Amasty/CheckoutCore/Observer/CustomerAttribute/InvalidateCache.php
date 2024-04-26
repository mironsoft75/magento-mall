<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Observer\CustomerAttribute;

use Amasty\CheckoutCore\Cache\InvalidateCheckoutCache;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class InvalidateCache implements ObserverInterface
{
    /**
     * @var InvalidateCheckoutCache
     */
    private $invalidateCheckoutCache;

    public function __construct(InvalidateCheckoutCache $invalidateCheckoutCache)
    {
        $this->invalidateCheckoutCache = $invalidateCheckoutCache;
    }

    /**
     * Event: customer_attributes_after_save
     *
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $this->invalidateCheckoutCache->execute();
    }
}
