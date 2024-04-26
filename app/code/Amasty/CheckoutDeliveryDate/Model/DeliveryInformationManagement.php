<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutDeliveryDate
*/

declare(strict_types=1);

namespace Amasty\CheckoutDeliveryDate\Model;

use Amasty\CheckoutDeliveryDate\Api\DeliveryInformationManagementInterface;
use Amasty\CheckoutDeliveryDate\Model\ResourceModel\Delivery as DeliveryResource;
use Magento\Framework\Escaper;

class DeliveryInformationManagement implements DeliveryInformationManagementInterface
{
    /**
     * @var DeliveryResource
     */
    private $deliveryResource;

    /**
     * @var DeliveryDateProvider
     */
    private $deliveryProvider;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        DeliveryResource $deliveryResource,
        DeliveryDateProvider $deliveryProvider,
        Escaper $escaper
    ) {
        $this->deliveryResource = $deliveryResource;
        $this->deliveryProvider = $deliveryProvider;
        $this->escaper = $escaper;
    }

    /**
     * @param int $cartId
     * @param string $date
     * @param int $time
     * @param string $comment
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function update($cartId, $date, $time = -1, $comment = ''): bool
    {
        $delivery = $this->deliveryProvider->findByQuoteId((int)$cartId);

        $delivery->addData([
            'date' => strtotime($date) ?: null,
            'time' => $time >= 0 ? $time : null,
            'comment' => ($comment) ? $this->escaper->escapeHtml($comment) : null
        ]);

        if ($delivery->getData('date') === null
            && $delivery->getData('time') === null
            && $delivery->getData('comment') === null
        ) {
            if ($delivery->getId()) {
                $this->deliveryResource->delete($delivery);
            }
        } else {
            $this->deliveryResource->save($delivery);
        }

        return true;
    }
}
