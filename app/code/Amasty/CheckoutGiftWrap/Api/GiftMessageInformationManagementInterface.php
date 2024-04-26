<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutGiftWrap
*/

declare(strict_types=1);

namespace Amasty\CheckoutGiftWrap\Api;

interface GiftMessageInformationManagementInterface
{
    /**
     * @param string $cartId
     * @param mixed $giftMessage
     *
     * @return bool
     */
    public function update($cartId, $giftMessage): bool;
}
