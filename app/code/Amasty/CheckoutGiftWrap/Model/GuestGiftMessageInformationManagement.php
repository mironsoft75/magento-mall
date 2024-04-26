<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutGiftWrap
*/

declare(strict_types=1);

namespace Amasty\CheckoutGiftWrap\Model;

use Amasty\CheckoutGiftWrap\Api\GiftMessageInformationManagementInterface;
use Amasty\CheckoutGiftWrap\Api\GuestGiftMessageInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestGiftMessageInformationManagement implements GuestGiftMessageInformationManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var GiftMessageInformationManagementInterface
     */
    protected $giftMessageInformationManagement;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        GiftMessageInformationManagementInterface $giftMessageInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->giftMessageInformationManagement = $giftMessageInformationManagement;
    }

    /**
     * @param string $cartId
     * @param mixed $giftMessage
     * @return bool
     */
    public function update($cartId, $giftMessage): bool
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->giftMessageInformationManagement->update(
            $quoteIdMask->getQuoteId(),
            $giftMessage
        );
    }
}
