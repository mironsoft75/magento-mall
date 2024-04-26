<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Test\Unit\Model;

use Amasty\CheckoutDeliveryDate\Model\DeliveryInformationManagement;
use Amasty\CheckoutCore\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class DeliveryInformationManagementTest
 *
 * @see DeliveryInformationManagement
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class DeliveryInformationManagementTest extends \PHPUnit\Framework\TestCase
{
    /**
     *  @covers DeliveryInformationManagement::update
     */
    public function testUpdate()
    {
        $escaper = $this->createMock(\Magento\Framework\Escaper::class);
        $deliveryObject = $this->createMock(\Amasty\CheckoutDeliveryDate\Model\Delivery::class);
        $deliveryProvider = $this->createMock(\Amasty\CheckoutDeliveryDate\Model\DeliveryDateProvider::class);
        $deliveryResource = $this->createMock(\Amasty\CheckoutDeliveryDate\Model\ResourceModel\Delivery::class);

        $deliveryProvider->method('findByQuoteId')->willReturn($deliveryObject);
        $deliveryResource->method('save');
        $deliveryResource->method('delete');

        $model = new DeliveryInformationManagement(
            $deliveryResource,
            $deliveryProvider,
            $escaper
        );

        $this->assertTrue($model->update(1, '15', 1, 'test'));

        $deliveryObject->setId(5);
        $this->assertTrue($model->update(1, '', null, null));
    }
}
