<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Smtp
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Smtp\Observer\Customer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ModelSaveBefore
 * @package Magetop\Smtp\Observer\Customer
 */
class ModelSaveBefore implements ObserverInterface
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * ModelSaveBefore constructor.
     *
     * @param CustomerFactory $customerFactory
     */
    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $dataObject = $observer->getEvent()->getDataObject();

        if (!$dataObject->getId()) {
            //isObjectNew can't use on this case
            $dataObject->setIsNewRecord(true);
        } elseif ($dataObject instanceof Customer) {
            $customOrigObject = $this->customerFactory->create()->load($dataObject->getId());
            $dataObject->setCustomOrigObject($customOrigObject);
        }
    }
}
