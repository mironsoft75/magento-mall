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

define([
    'mage/storage',
    'Magetop_Smtp/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote'
], function (storage, resourceUrlManager, quote) {
    'use strict';

    return function (address, isOsc) {
        return storage.post(
            resourceUrlManager.getUrlForUpdateOrder(quote),
            JSON.stringify({
                address: address,
                isOsc: isOsc
            }),
            false
        );
    };
});
